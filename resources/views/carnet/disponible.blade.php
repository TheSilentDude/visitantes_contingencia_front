<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Carnet de visitante disponible">
    <meta name="author" content="Sistema de Carnetización">

    <title>Carnet Disponible | Sistema de Carnetización</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">

    <style>
        body {
            padding-top: 20px;
        }
        
        .carnet-header {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 0.35rem 0.35rem 0 0;
        }

        .disponible-container {
            text-align: center;
            padding: 3rem;
        }

        .carnet-icon {
            font-size: 4rem;
            color: #36b9cc;
            margin-bottom: 1rem;
        }

        .piso-badge {
            background: #36b9cc;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 15px;
            font-size: 1rem;
            margin: 0.25rem;
            display: inline-block;
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
                                        <i class="fas fa-id-card me-2"></i>Carnet de Visitante
                                    </h1>
                                    <p class="mb-0">Estado del carnet</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carnet Disponible -->
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-info">
                                        <i class="fas fa-info-circle me-2"></i>Carnet Disponible
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="disponible-container">
                                        <i class="fas fa-id-card carnet-icon"></i>
                                        <h4 class="text-gray-900 mb-3">Carnet Disponible</h4>
                                        
                                        <div class="mb-4">
                                            <span class="piso-badge">
                                                <i class="fas fa-building"></i> Piso {{ $carnet->piso_asociado ?? $piso }}
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <strong>Código de Carnet:</strong> {{ $codigo_carnet }}
                                        </div>
                                        
                                        <p class="text-gray-600 mb-4">
                                            Este carnet está disponible para ser asignado a un visitante.
                                            Actualmente no hay ningún visitante registrado con este carnet.
                                        </p>
                                        
                                        <div class="alert alert-info">
                                            <i class="fas fa-lightbulb me-2"></i>
                                            <strong>Información:</strong> Para asignar este carnet a un visitante, 
                                            diríjase al módulo de recepción y registre un nuevo visitante.
                                        </div>
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