<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset styles */
        body,
        table,
        td,
        p,
        a,
        li,
        blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
        }

        /* Main styles */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(60, 132, 168, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #008dcc 0%, #008dcc 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="0,0 1000,0 1000,60 0,100"/></svg>') no-repeat;
            background-size: cover;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header-text {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-decoration: none;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header-subtitle {
            color: #e8f4f8;
            margin: 8px 0 0 0;
            font-size: 16px;
            font-weight: 400;
        }

        .content {
            padding: 48px 40px;
            background-color: #ffffff;
        }

        .content h1 {
            color: #2c3e50;
            margin-top: 0;
            font-size: 32px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 24px;
        }

        .code-container {
            background: linear-gradient(135deg, #e8f4f8 0%, #ffffff 100%);
            /* border: 3px solid #3d84a8;*/
            border-radius: 16px;
            padding: 40px 30px;
            text-align: center;
            margin: 32px 0;
            box-shadow: 0 8px 16px rgba(60, 132, 168, 0.15);
            position: relative;
            overflow: hidden;
        }

        .code-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #3d84a8, #5a9bc4, #46a85c);
            border-radius: 16px;
            z-index: -1;
        }

        .verification-code {
            font-size: 42px;
            font-weight: 800;
            color: #3d84a8;
            letter-spacing: 8px;
            margin: 16px 0;
            font-family: 'Courier New', monospace;
            text-shadow: 0 2px 4px rgba(60, 132, 168, 0.2);
            background: linear-gradient(135deg, #3d84a8 0%, #2c6280 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .code-label {
            margin: 0 0 12px 0;
            color: #7f8c8d;
            font-size: 16px;
            font-weight: 500;
        }

        .code-expiry {
            margin: 16px 0 0 0;
            color: #7f8c8d;
            font-size: 14px;
            font-weight: 400;
        }

        .security-box {
            background: linear-gradient(135deg, #fff8e1 0%, #fffbf0 100%);
            /*border: 2px solid #f39c12;*/
            border-radius: 12px;
            padding: 24px;
            margin: 32px 0;
            position: relative;
        }

        .security-box::before {
            content: '🛡️';
            position: absolute;
            top: -12px;
            left: 20px;
            background: #f39c12;
            padding: 8px 12px;
            border-radius: 50%;
            font-size: 16px;
        }

        .security-title {
            color: #e67e22;
            margin: 0 0 16px 0;
            font-size: 15px;
            font-weight: 600;
            padding-left: 20px;
        }

        .security-list {
            color: #e67e22;
            margin: 0;
            padding-left: 40px;
            /*list-style: none;*/
        }

        .security-list li {
            margin-bottom: 8px;
            position: relative;
            font-size: 12px;
            line-height: 1.5;
        }

        .security-list li::before {
            content: '✓';
            position: absolute;
            left: -20px;
            color: #27ae60;
            font-weight: bold;
        }

        .help-section {
            background-color: #e8f4f8;
            border-radius: 12px;
            padding: 24px;
            margin: 32px 0;
            text-align: center;
        }

        .help-link {
            color: #3d84a8;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 24px;
            background-color: #ffffff;
            border: 2px solid #3d84a8;
            border-radius: 8px;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .footer {
            background: linear-gradient(135deg, #008dcc 0%, #008dcc 100%);
            color: #ffffff;
            padding: 32px 40px;
            text-align: center;
            font-size: 13px;
        }

        .footer a {
            color: #e8f4f8;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .footer-links {
            margin: 16px 0 0 0;
            font-size: 12px;
        }

        .footer-links a {
            color: #e8f4f8;
            margin: 0 8px;
        }

        /* Responsive */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }

            .content {
                padding: 32px 24px !important;
            }

            .header {
                padding: 32px 24px !important;
            }

            .verification-code {
                font-size: 32px !important;
                letter-spacing: 4px !important;
            }

            .content h1 {
                font-size: 24px !important;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                @include('Emails.Components.logo')
                <p class="header-subtitle">
                    Sistema de Autenticación Seguro
                </p>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h1>Código de Verificación</h1>

            <p style="color: #7f8c8d; line-height: 1.7; font-size: 16px; text-align: center;">
                Estimado usuario,
            </p>

            <p style="color: #2c3e50; line-height: 1.7; font-size: 16px; text-align: center;">
                Has solicitado un código de verificación para acceder a tu cuenta en
                <strong style="color: #3d84a8;">{{ config('app.name') }}</strong>.
                Utiliza el siguiente código para completar el proceso:
            </p>

            <div class="code-container">
                <p class="code-label">
                    Tu código de verificación es:
                </p>
                <p class="verification-code">{{ $code }}</p>
                <p class="code-expiry">
                    ⏰ Válido por {{ $expires_in ?? '15 minutos' }}
                </p>
            </div>

            <div class="security-box">
                <h3 class="security-title">
                    Medidas de Seguridad
                </h3>
                <ul class="security-list">
                    <li>Este código expira en {{ $expires_in ?? '15 minutos' }}</li>
                    <li>Solo es válido para una sola verificación</li>
                    <li>Nunca compartas este código con terceros</li>
                    <li>Nuestro equipo nunca te pedirá este código por teléfono</li>
                </ul>
            </div>

            <p style="color: #7f8c8d; line-height: 1.7; font-size: 15px; text-align: center; margin: 32px 0;">
                Si no solicitaste este código, puedes ignorar este mensaje con seguridad.
                <br>Tu cuenta permanece protegida.
            </p>

            <div style="margin-top:10px">
                @include('Emails.Components.app')
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 16px 0; font-size: 13px; color: #e8f4f8;">
                Este es un mensaje automático de seguridad. Por favor, no respondas a este email.
            </p>
            <p style="margin: 0; color: #e8f4f8; font-size: 12px;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
            </p>

            <p class="footer-links">
                <a href="{{ config('app.url') }}/privacy">Política de Privacidad</a> |
                <a href="{{ config('app.url') }}/terms">Términos de Servicio</a> |
                <a href="{{ config('app.url') }}/unsubscribe">Darse de baja</a>
            </p>
        </div>
    </div>
</body>

</html>
