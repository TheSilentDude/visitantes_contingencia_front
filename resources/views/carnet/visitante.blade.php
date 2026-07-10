<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Información del carnet de visitante">
    <meta name="author" content="Sistema de Carnetización">

    <title>Carnet de Visitante | Sistema de Carnetización</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <!-- Fuente Georama para carnets -->
    <style>
        @font-face {
            font-family: 'Georama';
            src: url('{{ asset('font/Georama/Georama-VariableFont_wdth,wght.ttf') }}') format('truetype');
            font-weight: 100 900;
            font-stretch: 62.5% 150%;
            font-display: swap;
        }
    </style>

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">

    <!-- Estilos personalizados idénticos a carnet.php -->
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
        
        .icon-circle {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card {
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.1) !important;
        }
        
        .input-group-lg .form-control {
            font-size: 1.1rem;
            padding: 0.75rem 1rem;
        }
        
        .input-group-text {
            border: 1px solid #d1d3e2;
        }
        
        .border-start-0 {
            border-left: 0 !important;
        }
        
        .border-end-0 {
            border-right: 0 !important;
        }
        
        .opacity-75 {
            opacity: 0.75;
        }
        
        .text-md-end {
            text-align: right !important;
        }
        
        @media (max-width: 768px) {
            .text-md-end {
                text-align: left !important;
                margin-top: 1rem;
            }
            
            .icon-circle {
                width: 50px !important;
                height: 50px !important;
            }
            
            .icon-circle i {
                font-size: 1.5rem !important;
            }
        }
        
        /* Sin compensación para header ya que no hay header fijo */
        body {
            padding-top: 20px;
        }
        
        #content {
            margin-top: 0;
        }

        /* Estilos específicos para la vista del carnet */
        .carnet-header {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 0.35rem 0.35rem 0 0;
        }

        .user-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid #1cc88a;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            margin: 0 auto 1rem;
            display: block;
            object-fit: cover;
        }

        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }

        .piso-badge {
            background: #1cc88a;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            margin: 0.125rem;
            display: inline-block;
        }

        .equipo-item {
            border-left: 3px solid #1cc88a;
            padding-left: 1rem;
            margin-bottom: 0.5rem;
        }

        .error-container {
            text-align: center;
            padding: 3rem;
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    
                    <!-- Header del Carnet -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="carnet-header">
                                    <h1 class="h3 mb-2">
                                        <i class="fas fa-id-card-alt me-2"></i>Carnet de Visitante
                                    </h1>
                                    <p class="mb-0">Información verificada del visitante</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Columna izquierda: foto -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">Foto</h6>
                                </div>
                                <div class="card-body text-center">
                                    @if (!empty($visitante->url_img))
                                        <img src="{{ rtrim(config('services.backend.url'), '/') . '/' . ltrim($visitante->url_img, '/') }}" 
                                             alt="Foto del visitante" class="user-photo">
                                    @else
                                        <div class="user-photo d-flex align-items-center justify-content-center" 
                                             style="background: #e9ecef;">
                                            <i class="fas fa-user fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3">
                                        <span class="piso-badge">
                                            <i class="fas fa-building"></i> Piso {{ $visitante->piso_asociado ?? $visitante->piso }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Carnet #{{ $visitante->codigo_carnet }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: datos del visitante -->
                        <div class="col-md-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">Información del Visitante</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Cédula del Visitante</label>
                                            <input type="text" class="form-control" value="{{ $visitante->cedula ?? '' }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Primer Nombre</label>
                                            <input type="text" class="form-control" value="{{ $visitante->primer_nombre ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Segundo Nombre</label>
                                            <input type="text" class="form-control" value="{{ $visitante->segundo_nombre ?? '' }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Primer Apellido</label>
                                            <input type="text" class="form-control" value="{{ $visitante->primer_apellido ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Segundo Apellido</label>
                                            <input type="text" class="form-control" value="{{ $visitante->segundo_apellido ?? '' }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" value="{{ $visitante->telefono ?? 'No registrado' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Piso de Visita</label>
                                            <input type="text" class="form-control" value="Piso {{ $visitante->piso ?? '' }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Departamento</label>
                                            <input type="text" class="form-control" value="{{ $visitante->departamento ?? 'No especificado' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Estado del Carnet</label>
                                            <input type="text" class="form-control status-active" 
                                                   value="{{ $visitante->status ? 'Activo' : 'Inactivo' }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Código de Carnet Asignado</label>
                                            <input type="text" class="form-control" value="{{ $visitante->codigo_carnet ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nombre Empleado</label>
                                            <input type="text" class="form-control" value="{{ $visitante->nombre_empleado ?? '' }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Apellido Empleado</label>
                                            <input type="text" class="form-control" value="{{ $visitante->apellido_empleado ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Ente Empleado</label>
                                            <input type="text" class="form-control" value="{{ $visitante->ente_empleado ?? '' }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                        </div>
                                    </div>

                                    @if (!empty($visitante->equipos))
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Equipos Registrados</label>
                                            <div class="card">
                                                <div class="card-body">
                                                    @foreach ($visitante->equipos as $equipo)
                                                        <div class="equipo-item">
                                                            <strong>{{ $equipo->tipo }}</strong>
                                                            @if (!empty($equipo->marca))
                                                                - Marca: {{ $equipo->marca }}
                                                            @endif
                                                            @if (!empty($equipo->serial_equipo))
                                                                - Serial: {{ $equipo->serial_equipo }}
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="text-center mt-4">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            Información verificada y actualizada
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

</body>
</html>