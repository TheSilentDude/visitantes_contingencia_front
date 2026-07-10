<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Credenciales de Acceso</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #0056b3;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin-bottom: 10px;
            font-size: 15px;
        }
        .credential-label {
            font-weight: 600;
            color: #555;
            width: 100px;
            display: inline-block;
        }
        .credential-value {
            color: #333;
            font-family: monospace;
            font-size: 16px;
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            border: 1px solid #dee2e6;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #0056b3;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #004494;
        }
        .security-tips {
            font-size: 13px;
            color: #666;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .security-tips ul {
            margin: 5px 0 0 20px;
            padding: 0;
        }
        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sistema de Carnetización</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Hola, <strong>{{ $nombreCompleto }}</strong></p>
            
            <p>Bienvenido/a. Se le han asignado credenciales de acceso al sistema con el rol de <strong>{{ $rol }}</strong>.</p>
            
            <div class="credentials-box">
                <div class="credential-item">
                    <span class="credential-label">Usuario:</span>
                    <span class="credential-value">{{ $usuario }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Contraseña:</span>
                    <span class="credential-value">{{ $clave }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Rol:</span>
                    <span>{{ $rol }}</span>
                </div>
            </div>

            <div class="btn-container">
                <a href="{{ env('URL_SYSTEM') }}" class="btn">Iniciar Sesión</a>
            </div>
            
            <div class="security-tips">
                <strong>🔒 Recomendaciones de Seguridad:</strong>
                <ul>
                    <li>Cambie su contraseña inmediatamente después de su primer ingreso.</li>
                    <li>No comparta estas credenciales con nadie.</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>Este es un mensaje automático del Sistema de Carnetización del MINCYT.</p>
            <p>&copy; {{ date('Y') }} Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>