<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Estado del carnet de visitante">
    <meta name="author" content="Sistema de Carnetización">

    <title>{{ $titulo }} | Sistema de Carnetización</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        
        .error-icon.formato,
        .error-icon.enlace_invalido {
            color: #dc3545;
        }
        
        .error-icon.no_encontrado,
        .error-icon.desactualizado {
            color: #fd7e14;
        }
        
        .error-icon.expirado {
            color: #6f42c1;
        }
        
        .error-icon.sistema {
            color: #6c757d;
        }
        
        .error-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.75rem;
        }
        
        .error-message {
            color: #6c757d;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .contact-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .contact-info h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        
        .contact-info p {
            color: #6c757d;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .logo-mincyt {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <!-- Logo MINCYT -->
        <div class="logo-mincyt">
            <i class="fas fa-building"></i>
        </div>
        
        <!-- Icono de error -->
        <div class="error-icon {{ $tipo }}">
            @switch($tipo)
                @case('formato')
                @case('enlace_invalido')
                    <i class="fas fa-exclamation-triangle"></i>
                    @break
                @case('no_encontrado')
                @case('desactualizado')
                    <i class="fas fa-search"></i>
                    @break
                @case('expirado')
                    <i class="fas fa-clock"></i>
                    @break
                @case('sistema')
                    <i class="fas fa-cog"></i>
                    @break
                @default
                    <i class="fas fa-info-circle"></i>
            @endswitch
        </div>
        
        <!-- Título del error -->
        <h2 class="error-title">{{ $titulo }}</h2>
        
        <!-- Mensaje del error -->
        <p class="error-message">{{ $mensaje }}</p>
        
        <!-- Instrucciones según el tipo de error -->
        @switch($tipo)
            @case('formato')
            @case('enlace_invalido')
                <div class="alert alert-warning">
                    <i class="fas fa-qrcode me-2"></i>
                    <strong>Sugerencia:</strong> Asegúrese de escanear el código QR completo del carnet oficial.
                </div>
                @break
                
            @case('no_encontrado')
                <div class="alert alert-info">
                    <i class="fas fa-id-card me-2"></i>
                    <strong>¿Qué hacer?</strong> Diríjase a recepción para verificar el estado de su carnet.
                </div>
                @break
                
            @case('expirado')
                <div class="alert alert-primary">
                    <i class="fas fa-calendar me-2"></i>
                    <strong>Renovación:</strong> Su carnet necesita ser renovado. Contacte al personal de seguridad.
                </div>
                @break
                
            @case('desactualizado')
                <div class="alert alert-warning">
                    <i class="fas fa-sync me-2"></i>
                    <strong>Actualización:</strong> Solicite un nuevo carnet con el sistema actualizado.
                </div>
                @break
                
            @case('sistema')
                <div class="alert alert-secondary">
                    <i class="fas fa-tools me-2"></i>
                    <strong>Error técnico:</strong> Nuestro equipo ha sido notificado automáticamente.
                </div>
                @break
        @endswitch
        
        <!-- Información de contacto -->
        <div class="contact-info">
            <h6><i class="fas fa-headset me-2"></i>¿Necesita ayuda?</h6>
            <p><i class="fas fa-map-marker-alt me-2"></i>Diríjase a la recepción de la Torre Ministerial</p>
            <p><i class="fas fa-shield-alt me-2"></i>Contacte al personal de seguridad</p>
            <p><i class="fas fa-building me-2"></i>Ministerio del Poder Popular para Ciencia y Tecnología</p>
        </div>
        
        <!-- Botón para intentar de nuevo -->
        <div class="mt-4">
            <button class="btn btn-primary" onclick="window.location.reload()">
                <i class="fas fa-redo me-2"></i>Intentar de Nuevo
            </button>
        </div>
        
        <!-- Información adicional -->
        <div class="mt-4">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Sistema de Carnetización Digital - MINCYT<br>
                Código de referencia: {{ strtoupper(substr(md5(now()), 0, 8)) }}
            </small>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>