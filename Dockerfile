# syntax=docker/dockerfile:1.7
#
# Imagen de PRODUCCIÓN: sin Xdebug, sin Composer ni compiladores en runtime.
# Supervisor y Apache arrancan como root; peticiones y workers usan www-data.
#
#   docker build -f Dockerfile -t miapp:prod .
#   docker run -p 8083:80 -p 8443:443 --env-file .env.prod \
#     --mount type=bind,src=/ruta/server.pem,dst=/etc/ssl/certs/server.pem,readonly \
#     --mount type=bind,src=/ruta/server.key,dst=/etc/ssl/private/server.key,readonly miapp:prod
#
# Recomendado: fijar también el digest de la imagen base
#   php:8.4.27-apache-trixie@sha256:<digest>
ARG PHP_IMAGE=php:8.4.27-apache-trixie
# OCI8 salió del core en PHP 8.4: se instala desde PECL (3.4.x para PHP 8.4)
ARG OCI8_VERSION=3.4.0
ARG IC_DIR=/opt/oracle/instantclient_21_3

############################################
# 1. Compilar OCI8 (SDK y compiladores no llegan a la imagen final)
############################################
FROM ${PHP_IMAGE} AS oci8-build
ARG OCI8_VERSION
ARG IC_DIR

RUN apt-get update \
    && apt-get install -y --no-install-recommends $PHPIZE_DEPS unzip libaio1t64 \
    && rm -rf /var/lib/apt/lists/*

COPY docker/config/instantclient-basic-linux.x64-21.3.0.0.0.zip \
     docker/config/instantclient-sdk-linux.x64-21.3.0.0.0.zip /tmp/

RUN unzip -q /tmp/instantclient-basic-linux.x64-21.3.0.0.0.zip -d /opt/oracle \
    && unzip -q /tmp/instantclient-sdk-linux.x64-21.3.0.0.0.zip -d /opt/oracle \
    && rm /tmp/*.zip \
    && ln -sf /usr/lib/x86_64-linux-gnu/libaio.so.1t64 /usr/lib/x86_64-linux-gnu/libaio.so.1 \
    && echo "${IC_DIR}" > /etc/ld.so.conf.d/oracle-instantclient.conf && ldconfig \
    && echo "instantclient,${IC_DIR}" | pecl install oci8-${OCI8_VERSION} \
    && docker-php-ext-enable oci8 \
    && rm -rf "${IC_DIR}/sdk" \
    && ln -s "${IC_DIR}" /opt/oracle/instantclient

############################################
# 2. Runtime base: PHP + extensiones
############################################
FROM ${PHP_IMAGE} AS runtime
ARG IC_DIR

ENV TZ=America/Bogota \
    ORACLE_HOME=${IC_DIR}

# Rechazar bases con PHP vulnerable (CVE-2026-109618 / CVE-2026-109619)
RUN php -r 'if (version_compare(PHP_VERSION, "8.4.27", "<")) { fwrite(STDERR, "PHP >= 8.4.27 required, found " . PHP_VERSION . PHP_EOL); exit(1); }'

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# install-php-extensions elimina las dependencias de compilación al terminar
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
        opcache pdo_pgsql pgsql pdo_mysql mysqli mbstring zip exif pcntl bcmath gd sockets soap \
    && rm /usr/local/bin/install-php-extensions

# Runtime de Oracle: libaio + Instant Client "basic" (sin SDK)
RUN apt-get update \
    && apt-get install -y --no-install-recommends libaio1t64 curl supervisor locales \
    && echo 'en_US.UTF-8 UTF-8' > /etc/locale.gen \
    && locale-gen \
    && rm -rf /var/lib/apt/lists/* \
    && ln -sf /usr/lib/x86_64-linux-gnu/libaio.so.1t64 /usr/lib/x86_64-linux-gnu/libaio.so.1
COPY --from=oci8-build /opt/oracle /opt/oracle
COPY --from=oci8-build /etc/ld.so.conf.d/oracle-instantclient.conf /etc/ld.so.conf.d/
COPY --from=oci8-build /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=oci8-build /usr/local/etc/php/conf.d/docker-php-ext-oci8.ini /usr/local/etc/php/conf.d/
RUN ldconfig \
    && php -r 'if (!extension_loaded("oci8")) { fwrite(STDERR, "OCI8 is required\n"); exit(1); } echo oci_client_version(), PHP_EOL;'

# Apache: sin firma de versión, sin TRACE
RUN a2enmod rewrite headers ssl socache_shmcb \
    && a2dissite 000-default \
    && sed -ri \
        -e 's/^ServerTokens .*/ServerTokens Prod/' \
        -e 's/^ServerSignature .*/ServerSignature Off/' \
        -e 's/^TraceEnable .*/TraceEnable Off/' \
        /etc/apache2/conf-available/security.conf

COPY docker/config/apache-app.conf /etc/apache2/sites-available/app.conf
RUN a2ensite app

RUN mkdir -p /var/cache/php-soap && chown www-data:www-data /var/cache/php-soap && chmod 700 /var/cache/php-soap

# Configuración PHP de producción
COPY docker/config/php.ini-production /usr/local/etc/php/php.ini
COPY <<'EOF' /usr/local/etc/php/conf.d/zz-app.ini
date.timezone=America/Bogota
expose_php=Off
memory_limit=512M
upload_max_filesize=64M
post_max_size=64M
; Mitigación CVE-2026-109619 / CVE-2026-115560 (mantener hasta el próximo release de PHP)
session.upload_progress.enabled=0
opcache.enable=1
; El código es inmutable dentro de la imagen
opcache.validate_timestamps=0
opcache.jit=tracing
opcache.jit_buffer_size=256M
soap.wsdl_cache_enabled=1
soap.wsdl_cache_dir=/var/cache/php-soap
EOF

############################################
# 3. Dependencias Composer (Composer no llega a la imagen final)
############################################
FROM runtime AS vendor
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache \
    composer install --no-dev --prefer-dist --no-interaction --no-progress \
        --no-scripts --no-autoloader
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-interaction

############################################
# 4. Imagen final
############################################
FROM runtime

WORKDIR /var/www/html
EXPOSE 80 443

# Código propiedad de root => solo lectura para www-data
COPY --from=vendor /app /var/www/html
COPY docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views \
        storage/logs bootstrap/cache public/upload \
    && chmod -R go-w /var/www/html \
    && chown -R www-data:www-data storage bootstrap/cache public/upload \
    && chmod -R ug+rwX storage bootstrap/cache public/upload

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS --max-time 4 http://localhost/up -o /dev/null || exit 1

CMD ["/bin/sh", "-c", "apache2ctl -t && exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
