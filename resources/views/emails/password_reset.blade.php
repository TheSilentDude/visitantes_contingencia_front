<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f5f7;
            color: #51545e;
            margin: 0;
            padding: 0;
            width: 100%;
            -webkit-text-size-adjust: none;
        }
        .email-wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #f4f5f7;
        }
        .email-content {
            width: 100%;
            margin: 0;
            padding: 0;
            max-width: 570px;
            margin: 0 auto;
        }
        .email-body {
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            border: 1px solid #eaeaec;
            border-radius: 2px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
        }
        .email-body_inner {
            width: 570px;
            margin: 0 auto;
            padding: 35px;
            box-sizing: border-box;
        }
        h1 {
            margin-top: 0;
            color: #333333;
            font-size: 19px;
            font-weight: bold;
            text-align: left;
        }
        p {
            margin-top: 0;
            color: #51545e;
            font-size: 16px;
            line-height: 1.5em;
            text-align: left;
        }
        .button {
            display: inline-block;
            width: 200px;
            background-color: #2d3748;
            border-radius: 3px;
            color: #ffffff;
            font-size: 15px;
            line-height: 45px;
            text-align: center;
            text-decoration: none;
            -webkit-text-size-adjust: none;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .button--blue {
            background-color: #2d3748;
        }
        .footer {
            width: 570px;
            margin: 0 auto;
            padding: 0;
            text-align: center;
        }
        .footer p {
            color: #a8aaaf;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="email-content" width="100%" cellpadding="0" cellspacing="0">
                    <!-- Logo -->
                    <tr>
                        <td class="email-masthead" style="padding: 25px 0; text-align: center;">
                            <a href="{{ url('/') }}" style="font-size: 16px; font-weight: bold; color: #2d3748; text-decoration: none; text-shadow: 0 1px 0 white;">
                                Sistema de Carnetización
                            </a>
                        </td>
                    </tr>
                    <!-- Email Body -->
                    <tr>
                        <td class="email-body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="content-cell">
                                        <h1>¡Hola!</h1>
                                        <p>Estás recibiendo este correo porque recibimos una solicitud de restablecimiento de contraseña para tu cuenta.</p>
                                        <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $url }}" class="button button--blue" target="_blank">Restablecer Contraseña</a>
                                                </td>
                                            </tr>
                                        </table>
                                        <p>Este enlace de restablecimiento de contraseña expirará en 60 minutos.</p>
                                        <p>Si no solicitaste un restablecimiento de contraseña, no es necesario realizar ninguna otra acción.</p>
                                        <p>Saludos,<br>Sistema de Carnetización</p>
                                        <table class="subcopy" width="100%" cellpadding="0" cellspacing="0" style="border-top: 1px solid #eaeaec; margin-top: 25px; padding-top: 25px;">
                                            <tr>
                                                <td>
                                                    <p style="font-size: 12px;">Si tienes problemas haciendo clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador web: <a href="{{ $url }}" style="color: #3869d4;">{{ $url }}</a></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="content-cell" align="center" style="padding: 35px;">
                                        <p>&copy; {{ date('Y') }} Sistema de Carnetización. Todos los derechos reservados.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
