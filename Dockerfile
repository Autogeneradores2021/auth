FROM php:8.3-apache

# Actualiza zona horaria
ENV TZ=America/Bogota
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Establecer parámetros principales
ARG environment

ENV ENV=$environment
ENV APP_HOME /var/www/html
ENV USERNAME=www-data
ENV USER=root

# check environment
RUN if [ "$ENV" = "staging" ]; then echo "Building staging environment."; \
    elif [ "$ENV" = "production" ]; then echo "Building production environment."; \
    else echo "Set correct ENV in docker build-args like --build-arg ENV=dev. Available choices are staging, production." && exit 1; \
    fi

# instalacion de dependencias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libaio-dev \
    libzip-dev \
    vim \
    libcurl4-openssl-dev \
    supervisor


# Se instala manualmente libaio1, requerido por Oracle Instant Client
RUN apt-get update && apt-get install -y wget \
    && wget http://ftp.debian.org/debian/pool/main/liba/libaio/libaio1_0.3.112-9_amd64.deb -O /tmp/libaio1.deb \
    && dpkg -i /tmp/libaio1.deb \
    && rm /tmp/libaio1.deb

# Instala locales
RUN apt-get install -y locales && echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && locale-gen

# Install composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer

# Descarga los archivos de Oracle Instant Client
COPY ./docker/config/instantclient-basic-linux.x64-21.3.0.0.0.zip /tmp/
COPY ./docker/config/instantclient-sdk-linux.x64-21.3.0.0.0.zip /tmp/

# Descomprime los archivos de Oracle Instant Client
RUN unzip /tmp/instantclient-basic-linux.x64-21.3.0.0.0.zip -d /opt/oracle \
    && unzip /tmp/instantclient-sdk-linux.x64-21.3.0.0.0.zip -d /opt/oracle \
    && ln -s /opt/oracle/instantclient_21_3 /opt/oracle/instantclient \
    && rm /tmp/instantclient-basic-linux.x64-21.3.0.0.0.zip \
    && rm /tmp/instantclient-sdk-linux.x64-21.3.0.0.0.zip


COPY ./docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf


# Configurando Oracle Instant Client
RUN sh -c "echo /opt/oracle/instantclient_21_3 > /etc/ld.so.conf.d/oracle-instantclient.conf"
RUN ldconfig


ENV ORACLE_HOME /opt/oracle/instantclient_19_19


# Agregando variables de entorno
ENV PATH=$ORACLE_HOME:/usr/sbin:$PATH \
    LD_LIBRARY_PATH=$ORACLE_HOME:/usr/lib \
    CLASSPATH=$ORACLE_HOME


#Habilitar extensiones
RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/opt/oracle/instantclient_21_3
RUN docker-php-ext-install curl pdo pdo_mysql mysqli mbstring exif pcntl bcmath gd sockets oci8 zip


# limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*


# 3. mod_rewrite for URL rewrite and mod_headers for .htaccess extra headers like Access-Control-Allow-Origin-
RUN a2enmod rewrite headers

RUN chmod -R 777 /var/www/html

# 4. start with base php config, then add extensions
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"


# Deshabilitar el sitio predeterminado y eliminar todos los archivos predeterminados dentro de APP_HOME
RUN a2dissite 000-default.conf
RUN rm -r $APP_HOME

#Copiamos los certificados
COPY ./docker/ssl/server.pem /etc/ssl/certs/server.pem
COPY ./docker/ssl/server.key /etc/ssl/private/server.key
COPY ./docker/config/php.ini-production /usr/local/etc/php/php.ini

# Cambia los valores de los certificados y claves en el archivo de configuracion de apache
RUN sed -i '/SSLCertificateFile.*snakeoil\.pem/c\SSLCertificateFile \/etc\/ssl\/certs\/server.pem' /etc/apache2/sites-available/default-ssl.conf
RUN sed -i '/SSLCertificateKeyFile.*snakeoil\.key/cSSLCertificateKeyFile /etc/ssl/private/server.key\' /etc/apache2/sites-available/default-ssl.conf

# Habilitar módulos apache
RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2enmod socache_shmcb
RUN a2ensite default-ssl

#cambiar directorio de trabajo
USER $USER
WORKDIR $APP_HOME


#Copiar archivos fuente y archivo de configuración
COPY --chown=${USERNAME}:${USERNAME} . $APP_HOME/

#Crear upload
RUN mkdir -p public/upload

#Permisos
RUN chmod -R 777 $APP_HOME/storage
RUN chmod -R 777 $APP_HOME/bootstrap

#instalar dependencias del proyecto
RUN composer install --optimize-autoloader --no-dev

#Limpiar cache
#RUN php artisan optimize:clear

#EXPOSE 8017

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
