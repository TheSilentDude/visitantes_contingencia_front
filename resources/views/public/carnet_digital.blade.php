<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Información del carnet de identificación del empleado">
    <meta name="author" content="Sistema de Carnetización">

    <title>Carnet de Identificación | Sistema de Carnetización</title>

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

    <!-- Estilos personalizados idénticos a rrhh2.php -->
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
        
        /* Compensación para el header fixed */
        body {
            padding-top: 80px;
        }
        
        #content {
            margin-top: 0;
        }

        /* Estilos específicos para la vista del carnet */
        .carnet-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 0.35rem 0.35rem 0 0;
        }

        .user-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid #4e73df;
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
            background: #4e73df;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
            margin: 0.125rem;
            display: inline-block;
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
                                        <i class="fas fa-id-card me-2"></i>Carnet de Identificación
                                    </h1>
                                    <p class="mb-0">Información verificada del empleado</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Columna izquierda: foto -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Foto</h6>
                                </div>
                                <div class="card-body text-center">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="Foto del empleado" class="user-photo">
                                    @else
                                        <div class="user-photo d-flex align-items-center justify-content-center" 
                                             style="background: #e9ecef;">
                                            <i class="fas fa-user fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    @if($usuario->is_active)
                                        <div class="mt-3 status-active">
                                            <i class="fas fa-check-circle me-1"></i> Estado: Activo
                                        </div>
                                    @else
                                        <div class="mt-3 status-inactive">
                                            <i class="fas fa-times-circle me-1"></i> Estado: Inactivo
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: datos del empleado -->
                        <div class="col-md-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Información del Usuario</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Cédula del empleado</label>
                                            <input type="text" class="form-control" value="{{ $usuario->cedula }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Primer Nombre</label>
                                            <input type="text" class="form-control" value="{{ $usuario->primer_nombre }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Segundo Nombre</label>
                                            <input type="text" class="form-control" value="{{ $usuario->segundo_nombre }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Primer Apellido</label>
                                            <input type="text" class="form-control" value="{{ $usuario->primer_apellido }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Segundo Apellido</label>
                                            <input type="text" class="form-control" value="{{ $usuario->segundo_apellido }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Correo</label>
                                            <input type="text" class="form-control" value="{{ strtolower(substr($usuario->primer_nombre, 0, 1) . $usuario->primer_apellido) }}@mincyt.gob.ve" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Departamento</label>
                                            <input type="text" class="form-control" value="{{ $usuario->departamento ? $usuario->departamento->descripcion : 'No asignado' }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Cargo</label>
                                            <input type="text" class="form-control" value="{{ $usuario->cargo ? $usuario->cargo->descripcion : 'No asignado' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6 class="font-weight-bold text-primary mb-2">Pisos de Acceso Permitidos:</h6>
                                            @if(isset($pisos) && count($pisos) > 0)
                                                @foreach($pisos as $piso)
                                                    <span class="piso-badge">Piso {{ $piso }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Sin permisos de acceso especiales</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <h6 class="font-weight-bold text-primary mb-3">En Caso de Extravio:</h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Telefono de Contacto</label>
                                            <input type="text" class="form-control" value="02125358250" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Direccion Laboral</label>
                                            <input type="text" class="form-control" value="Av. Universidad, Esquina El Chorro, Torre Ministerial, Caracas 1010, Distrito Capital" readonly>
                                        </div>
                                    </div>

                                    

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
