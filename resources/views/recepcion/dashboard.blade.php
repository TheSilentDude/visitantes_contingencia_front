@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800"><i class="fas fa-building me-2"></i>  Panel de Recepción</h1>
        @if($canRegister ?? false)
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#searchEmployeeModal">
                <i class="fas fa-user-plus me-1"></i> Registrar Nuevo Visitante
            </button>
        @endif
    </div>

    @if(!($canRegister ?? false) && ($canViewList ?? false))
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Modo solo lectura:</strong> puedes consultar el listado y el detalle de visitantes. No está habilitado registrar nuevos visitantes ni registrar salidas.
        </div>
    @endif


    @if($canViewStats ?? false)
    <!-- Statistics Cards Row -->
    <div class="row mb-4">
        <!-- Total Visitantes -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Visitantes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalVisitantes) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entradas Hoy -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Entradas Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($entradasHoy) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salidas Hoy -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Salidas Hoy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($salidasHoy) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-closed fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    @endif

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('recepcion.dashboard.filtrar') }}" id="filtrosForm">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="filtro_nombre" class="form-label">Buscar por Nombre, cédula o motivo</label>
                        <input type="text" class="form-control" id="filtro_nombre" name="nombre" 
                               placeholder="Búsqueda..." 
                               value="{{ old('nombre', request('nombre')) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="filtro_fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="filtro_fecha" name="fecha" 
                               value="{{ old('fecha', request('fecha', date('Y-m-d'))) }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="filtro_estado" class="form-label">Estado</label>
                        <select class="form-control" id="filtro_estado" name="estado">
                            <option value="activos" {{ old('estado', request('estado', 'activos')) == 'activos' ? 'selected' : '' }}>Solo Activos</option>
                            <option value="todos" {{ old('estado', request('estado')) == 'todos' ? 'selected' : '' }}>Todos (Entradas y Salidas)</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>  Filtrar
                            </button>
                            <a href="{{ route('recepcion.dashboard.limpiar') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Limpiar Filtros
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Visitantes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i>
                {{ request('estado') == 'todos' ? 'Registro de Visitantes' : 'Visitantes Activos en la Torre' }}
                <span class="badge badge-primary ml-2">{{ count($visitantes) }}</span>
            </h6>
        </div>
        <div class="card-body">
            @if(empty($visitantes))
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay visitantes que mostrar con los filtros seleccionados.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped" id="visitantesTable">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Institución de Origen</th>
                                <th>Estado</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th style="width: 200px;">Motivo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visitantes as $visitante)
                            <tr>
                                <td>
                                    <strong>{{ $visitante->cedula }}</strong>
                                    @if($visitante->codigo_carnet)
                                        <br><small class="text-muted">Carnet #{{ $visitante->codigo_carnet }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $visitante->primer_nombre }} {{ $visitante->primer_apellido }}</strong>
                                        @if($visitante->segundo_nombre || $visitante->segundo_apellido)
                                            <br><small class="text-muted">{{ $visitante->segundo_nombre }} {{ $visitante->segundo_apellido }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($visitante->institucion_de_origen)
                                        <span class="badge badge-info">{{ $visitante->institucion_de_origen }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($visitante->estado_actual == 'DENTRO')
                                        <span class="badge badge-success">
                                            <i class="fas fa-user-check me-1"></i> EN TORRE
                                        </span>
                                        @if(isset($visitante->ingresa_con_vehiculo) && $visitante->ingresa_con_vehiculo)
                                            <br><span class="badge badge-warning text-dark mt-1"><i class="fas fa-car me-1"></i> CON VEHÍCULO</span>
                                        @endif
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-user-times me-1"></i>
                                            @if(isset($visitante->ingresa_con_vehiculo) && $visitante->ingresa_con_vehiculo)
                                                Fuera desde estacionamiento
                                            @else
                                                Fuera
                                            @endif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($visitante->fecha_entrada)
                                        <div class="text-success">
                                            <i class="fas fa-sign-in-alt me-1"></i>
                                            <strong>{{ \Carbon\Carbon::parse($visitante->fecha_entrada)->format('H:i') }}</strong>
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($visitante->fecha_entrada)->format('d/m/Y') }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($visitante->fecha_salida)
                                        <div class="text-danger">
                                            <i class="fas fa-sign-out-alt me-1"></i>
                                            <strong>{{ \Carbon\Carbon::parse($visitante->fecha_salida)->format('H:i') }}</strong>
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($visitante->fecha_salida)->format('d/m/Y') }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td style="width: 200px; word-wrap: break-word;">
                                    @if($visitante->descripcion)
                                        <small class="text-muted">{{ $visitante->descripcion }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm me-2" 
                                            onclick="mostrarDetalleVisitante({{ $visitante->id }}, {{ $visitante->id_entrada }})"
                                            title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    @if($visitante->estado_actual == 'DENTRO' && ($canRegister ?? false) && (!isset($visitante->ingresa_con_vehiculo) || !$visitante->ingresa_con_vehiculo))
                                        <button type="button" class="btn btn-danger btn-sm" title="Registrar salida"
                                                onclick="confirmarSalida({{ $visitante->id }}, '{{ addslashes($visitante->primer_nombre . ' ' . $visitante->primer_apellido) }}')">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Confirmación de Salida -->
    <div class="modal fade" id="confirmarSalidaModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-sign-out-alt me-2"></i> Confirmar Salida
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-sign-out-alt fa-4x text-danger opacity-50"></i>
                    </div>
                    <h5 class="mb-2">¿Confirmar registro de salida?</h5>
                    <p class="mb-0 text-gray-800">¿Deseas registrar la salida del visitante <strong id="nombreVisitanteSalida" class="text-primary"></strong>?</p>
                </div>
                <div class="modal-footer bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary px-4 me-2" data-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <form id="formConfirmarSalida" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4" id="btnProcesarSalida">
                            <i class="fas fa-check me-1"></i> Sí, confirmar salida
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalles del Visitante -->
    <div class="modal fade" id="detalleVisitanteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="height: 80vh;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>  Detalles del Visitante
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detalleVisitanteContent" style="max-height: calc(80vh - 120px); overflow-y: auto;">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p class="mt-2">Cargando información...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times me-1"></i>  Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div> 

    @if($canRegister ?? false)
    <!-- Modal: Búsqueda de Empleado -->
    <div class="modal fade" id="searchEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="searchEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="searchEmployeeModalLabel">
                        <i class="fas fa-search me-2"></i> Buscar Miembro de Comite
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        <i class="fas fa-info-circle"></i> Busque al empleado que recibirá la visita por nombre o cédula
                    </p>
                    
                    <div class="form-group">
                        <label for="employeeSearchInput">Nombre o Cédula del Empleado</label>
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" 
                                   class="form-control" 
                                   id="employeeSearchInput" 
                                   placeholder="Ej: Juan Pérez o 12345678"
                                   autocomplete="off">
                        </div>
                        <small class="text-muted">Mínimo 3 caracteres para buscar</small>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="searchLoading" class="text-center my-3" style="display:none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Buscando...</span>
                        </div>
                        <p class="mt-2">Buscando empleado...</p>
                    </div>

                    <!-- Results List -->
                    <div id="employeeResults" class="list-group mt-3" style="max-height: 400px; overflow-y: auto;"></div>

                    <!-- No Results Message -->
                    <div id="noResults" class="alert alert-info mt-3" style="display:none;">
                        <i class="fas fa-info-circle"></i> No se encontraron empleados con ese criterio de búsqueda.
                    </div>

                    <!-- Selected Employee Details Panel -->
                    <div id="selectedEmployeeDetails" style="display:none;">
                        <hr class="my-4">
                        <h6 class="mb-3 text-primary">
                            <i class="fas fa-user-check me-2"></i>  Datos del Usuario
                        </h6>
                        
                        <div class="card border-primary">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Cédula:</label>
                                        <div class="font-weight-bold" id="selectedCedula"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Fecha de Nacimiento:</label>
                                        <div class="font-weight-bold" id="selectedFechaNac"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Primer Nombre:</label>
                                        <div class="font-weight-bold" id="selectedPrimerNombre"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Segundo Nombre:</label>
                                        <div class="font-weight-bold" id="selectedSegundoNombre"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Primer Apellido:</label>
                                        <div class="font-weight-bold" id="selectedPrimerApellido"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Segundo Apellido:</label>
                                        <div class="font-weight-bold" id="selectedSegundoApellido"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Correo:</label>
                                        <div class="font-weight-bold" id="selectedCorreo"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small mb-1">Ente:</label>
                                        <div class="font-weight-bold" id="selectedEnte"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                    <button type="button" class="btn btn-primary" id="agregarVisitanteBtn" style="display:none;">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const employeeSearchInput = document.getElementById('employeeSearchInput');
    const employeeResults = document.getElementById('employeeResults');
    const searchLoading = document.getElementById('searchLoading');
    const noResults = document.getElementById('noResults');

    // Check if all required elements exist
    if (!employeeSearchInput || !employeeResults || !searchLoading || !noResults) {
        console.error('Employee search elements not found');
        return;
    }

    // Clear previous search when modal opens
    $('#employeeSearchModal').on('shown.bs.modal', function () {
        employeeSearchInput.value = '';
        employeeResults.innerHTML = '';
        noResults.style.display = 'none';
        searchLoading.style.display = 'none';
        const detailsPanel = document.getElementById('selectedEmployeeDetails');
        if (detailsPanel) {
            detailsPanel.style.display = 'none';
        }
        const agregarBtn = document.getElementById('agregarVisitanteBtn');
        if (agregarBtn) {
            agregarBtn.style.display = 'none';
        }
        employeeSearchInput.focus();
    });

    // Search on input with debounce
    employeeSearchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        // Clear previous results and loading
        employeeResults.innerHTML = '';
        noResults.style.display = 'none';
        searchLoading.style.display = 'none';
        
        // If less than 3 characters, just clear and return
        if (query.length < 3) {
            return;
        }
         
        // Show loading
        searchLoading.style.display = 'block';
        
        searchTimeout = setTimeout(() => {
            const url = `{{ route('visitantes.searchEmployee') }}?q=${encodeURIComponent(query)}`;
            console.log('--- EMPLOYEE SEARCH DEBUG ---');
            console.log('Fetching:', url);
            
            fetch(url)
                .then(async response => {
                    const text = await response.text();
                    console.log('Frontend response status:', response.status);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Failed to parse frontend JSON:', text);
                        throw e;
                    }
                })
                .then(data => {
                    console.log('Data from frontend controller:', data);
                    searchLoading.style.display = 'none';
                    
                    if (data.ok && data.data && data.data.length > 0) {
                        displayResults(data.data);
                    } else {
                        noResults.style.display = 'block';
                        // Display detailed errors if available
                        if (data.errors && data.errors.length > 0) {
                            let errorHtml = '<div class="mt-2 small text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Detalles técnicos:<ul>';
                            data.errors.forEach(err => {
                                errorHtml += `<li><strong>${err.api}:</strong> ${err.message}</li>`;
                            });
                            errorHtml += '</ul></div>';
                            noResults.innerHTML = '<i class="fas fa-info-circle"></i> No se encontraron empleados con ese criterio de búsqueda.' + errorHtml;
                        } else {
                            noResults.innerHTML = '<i class="fas fa-info-circle"></i> No se encontraron empleados con ese criterio de búsqueda.';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchLoading.style.display = 'none';
                    noResults.style.display = 'block';
                });
        }, 1000); // Debounce 1 second to avoid rate limiting
    });

    function displayResults(employees) {
        employeeResults.innerHTML = '';
        
        employees.forEach(emp => {
            const fullName = [emp.primer_nombre, emp.segundo_nombre, emp.primer_apellido, emp.segundo_apellido]
                .filter(n => n)
                .join(' ');
            
            const item = document.createElement('a');
            item.href = '#';
            item.className = 'list-group-item list-group-item-action';
            item.innerHTML = `
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1"><i class="fas fa-user text-primary me-2"></i>${fullName}</h6>
                        <small class="text-muted">
                            <i class="fas fa-id-card me-1"></i> C.I: ${emp.cedula || 'N/A'}
                        </small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary select-employee-btn">
                        <i class="fas fa-check"></i> Seleccionar
                    </button>
                </div>
            `;
            
            // Add click event to select button
            const selectBtn = item.querySelector('.select-employee-btn');
            selectBtn.addEventListener('click', function(e) {
                e.preventDefault();
                selectEmployee(emp);
            });
            
            employeeResults.appendChild(item);
        });
    }

    function selectEmployee(employee) {
        console.log('selectEmployee called with:', employee);
        
        // Hide search results
        employeeResults.innerHTML = '';
        noResults.style.display = 'none';
        
        // Show loading while fetching complete data
        searchLoading.style.display = 'block';
        
        const url = `{{ route('visitantes.getEmployeeData') }}?cedula=${employee.cedula}`;
        console.log('Fetching from URL:', url);
        
        // Fetch complete employee data using dedicated endpoint
        fetch(url)
            .then(response => {
                console.log('Response received:', response);
                return response.json();
            })
            .then(data => {
                console.log('Data parsed:', data);
                searchLoading.style.display = 'none';
                
                if (data.ok && data.data) {
                    const completeEmployee = data.data;
                    console.log('Complete employee data:', completeEmployee);
                    
                    // Store complete employee data in PHP session via AJAX
                    fetch('{{ route("recepcion.store-selected-employee") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(completeEmployee)
                    }).then(response => response.json())
                      .then(result => {
                          console.log('Employee stored in session:', result);
                      })
                      .catch(error => {
                          console.error('Error storing employee in session:', error);
                      });
                    
                    // Populate employee details panel
                    document.getElementById('selectedCedula').textContent = completeEmployee.cedula || 'N/A';
                    document.getElementById('selectedFechaNac').textContent = completeEmployee.fecha_nacimiento || 'N/A';
                    document.getElementById('selectedPrimerNombre').textContent = completeEmployee.primer_nombre || 'N/A';
                    document.getElementById('selectedSegundoNombre').textContent = completeEmployee.segundo_nombre || '-';
                    document.getElementById('selectedPrimerApellido').textContent = completeEmployee.primer_apellido || 'N/A';
                    document.getElementById('selectedSegundoApellido').textContent = completeEmployee.segundo_apellido || '-';
                    document.getElementById('selectedCorreo').textContent = completeEmployee.correo || 'N/A';
                    document.getElementById('selectedEnte').textContent = completeEmployee.ente || 'MINCYT';
                    
                    // Show employee details panel
                    document.getElementById('selectedEmployeeDetails').style.display = 'block';
                    document.getElementById('agregarVisitanteBtn').style.display = 'inline-block';
                    console.log('Employee details panel shown');
                } else {
                    console.error('Invalid response data:', data);
                    alert('No se pudieron obtener los datos completos del empleado');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                searchLoading.style.display = 'none';
                alert('Error al obtener los datos del empleado: ' + error.message);
            });
    }
    
    // Handle "Agregar" button click
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'agregarVisitanteBtn') {
            // Redirect to visitor registration form
            window.location.href = '{{ route("visitantes.create") }}?source=recepcion';
        }
    });
});

// Función para mostrar detalles del visitante en modal
function mostrarDetalleVisitante(visitanteId, transaccionId) {
    // Mostrar modal
    $('#detalleVisitanteModal').modal('show');
    
    // Mostrar loading
    $('#detalleVisitanteContent').html(`
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Cargando información...</p>
        </div>
    `);
    
    // Obtener datos del visitante
    let url = `/api/visitante-detalle/${visitanteId}`;
    if (transaccionId) {
        url += `?transaccion_id=${transaccionId}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const visitante = data.data.visitante;
                const equipos = data.data.equipos;
                 
                let equiposHtml = '';
                if (equipos && equipos.length > 0) {
                    equiposHtml = `
                        <div class="col-12 mb-3">
                            <h6 class="font-weight-bold text-primary">Equipos Registrados</h6>
                            <div class="card">
                                <div class="card-body">
                                    ${equipos.map(equipo => `
                                        <div class="border-left-success p-3 mb-2">
                                            <strong>${equipo.tipo}</strong>
                                            ${equipo.marca ? `<br><small>Marca: ${equipo.marca}</small>` : ''}
                                            ${equipo.serial_equipo ? `<br><small>Serial: ${equipo.serial_equipo}</small>` : ''}
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                let vehiculoHtml = '';
                const vehiculo = data.data.vehiculo;
                if (vehiculo) {
                    vehiculoHtml = `
                        <div class="col-12 mb-3">
                            <h5 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-car me-2"></i> Información del Vehículo
                            </h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <strong>Placa:</strong> <span class="badge badge-dark" style="font-size: 1.1em;">${vehiculo.placa}</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Tipo:</strong> ${vehiculo.tipo || 'N/A'}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Marca:</strong> ${vehiculo.marca || 'N/A'}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Modelo:</strong> ${vehiculo.modelo || 'N/A'}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Color:</strong> ${vehiculo.color || 'N/A'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                const contenido = `
                    <div class="row">
                        <!-- Foto -->
                        <div class="col-md-4 text-center mb-4">
                            ${visitante.url_img ? 
                                `<img src="{{ config('services.backend.url') }}/${visitante.url_img}" alt="Foto del visitante" class="img-thumbnail" style="max-width: 200px; max-height: 200px; object-fit: cover;">` :
                                `<div class="bg-light d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; margin: 0 auto;"><i class="fas fa-user fa-3x text-muted"></i></div>`
                            }
                            <div class="mt-3">
                                <span class="badge badge-info">Carnet #${visitante.codigo_carnet || 'N/A'}</span>
                                <br><span class="badge badge-success mt-1">Piso ${visitante.piso}</span>
                            </div>
                        </div>
                        
                        <!-- Información Personal -->
                        <div class="col-md-8">
                            <h5 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-user me-2"></i>  Información Personal
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Cédula:</strong> ${visitante.cedula || 'N/A'}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Teléfono:</strong> ${visitante.telefono || 'N/A'}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Primer Nombre:</strong> ${visitante.primer_nombre || ''}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Segundo Nombre:</strong> ${visitante.segundo_nombre || 'N/A'}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Primer Apellido:</strong> ${visitante.primer_apellido || ''}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Segundo Apellido:</strong> ${visitante.segundo_apellido || 'N/A'}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Departamento:</strong> ${visitante.departamento || 'N/A'}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Estado:</strong> 
                                    <span class="badge ${visitante.status ? 'badge-success' : 'badge-danger'}">
                                        ${visitante.status ? 'Activo' : 'Inactivo'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Información de Visita -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-clock me-2"></i>  Información de Visita
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Fecha/Hora Entrada:</strong><br>
                                    <i class="fas fa-sign-in-alt text-success me-1"></i>
                                    ${visitante.entrada_formatted || 'N/A'}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Fecha/Hora Salida:</strong><br>
                                    ${visitante.salida_formatted ? 
                                        `<i class="fas fa-sign-out-alt text-danger me-1"></i>${visitante.salida_formatted}` :
                                        '<span class="badge badge-success">En Torre</span>'
                                    }
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Empleado Responsable:</strong><br>
                                    ${visitante.nombre_empleado || ''} ${visitante.apellido_empleado || ''}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Ente:</strong><br>
                                    ${visitante.ente_empleado || 'MINCYT'}
                                </div>
                                ${visitante.motivo_visita ? `
                                    <div class="col-12 mb-2">
                                        <strong>Motivo de Visita:</strong><br>
                                        ${visitante.motivo_visita}
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                    
                    ${vehiculoHtml ? `<hr>${vehiculoHtml}` : ''}
                    ${equiposHtml ? `<hr>${equiposHtml}` : ''}
                `;
                
                $('#detalleVisitanteContent').html(contenido);
            } else {
                $('#detalleVisitanteContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${data.message || 'Error al cargar los detalles del visitante'}
                    </div>
                `);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            $('#detalleVisitanteContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error de conexión. Por favor intente nuevamente.
                </div>
            `);
        });
}

// Función para confirmar la salida
function confirmarSalida(id, nombre) {
    $('#nombreVisitanteSalida').text(nombre);
    
    // Construir la URL de checkout (asumiendo que sigue el patrón /visitantes/{id}/checkout)
    const form = document.getElementById('formConfirmarSalida');
    // Generar la URL base reemplazando un ID genérico proporcionado por route() no podemos usar Blade dentro de JS facilmente, 
    // así que construimos la URL usando string concatenation.
    form.action = '/visitantes/' + id + '/checkout';
    
    $('#confirmarSalidaModal').modal('show');
    
    // Evitar doble envío
    form.onsubmit = function() {
        const btn = document.getElementById('btnProcesarSalida');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';
    };
}

// Auto-submit form when filters change
$(document).ready(function() {
    $('#filtro_fecha, #filtro_piso, #filtro_estado').on('change', function() {
        $('#filtrosForm').submit();
    });
    
    // Búsqueda rápida (local en vivo) sin recargar página
    $('#filtro_nombre').on('input', function() {
        const query = $(this).val().toLowerCase().trim();
        
        // Filtrar las filas de la tabla instantáneamente
        $('#visitantesTable tbody tr').each(function() {
            // Tomamos todo el contenido de la fila para permitir búsqueda también por cédula
            const rowText = $(this).text().toLowerCase();
            
            if (rowText.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Prevenir que presionar "Enter" recargue la página si el usuario sólo quería filtrar
    $('#filtro_nombre').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
        }
    });
    
    // Prevenir envío múltiple del formulario
    $('#filtrosForm').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        setTimeout(() => {
            submitBtn.prop('disabled', false);
        }, 1000);
    });
});
</script>
@endpush
@endsection

@if(isset($stuckVisitors) && !empty($stuckVisitors))
<!-- Stuck Visitors Modal -->
<div class="modal fade" id="stuckVisitorsModal" tabindex="-1" role="dialog" aria-labelledby="stuckVisitorsModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="stuckVisitorsModalLabel">
            <i class="fas fa-exclamation-triangle mr-2"></i> Visitantes Pendientes de Salida
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning">
            <strong>Atención:</strong> Los siguientes visitantes llevan más de un dia con entrada registrada y no se ha marcado su salida:
        </div>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Fecha Entrada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stuckVisitors as $v)
                    <tr>
                        <td>{{ $v->cedula }}</td>
                        <td>{{ $v->primer_nombre }}</td>
                        <td>{{ $v->primer_apellido }}</td>
                        <td>{{ \Carbon\Carbon::parse($v->entrada_time)->format('d/m/Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Entendido</button>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#stuckVisitorsModal').modal('show');
    });
</script>
@endif
