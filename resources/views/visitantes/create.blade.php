@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-plus me-2"></i>  Agregar Visitante
                    </h6>
                </div> 
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Éxito:</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Por favor revise los siguientes campos:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div id="notifVisitante"></div>

                    <form action="{{ route('visitantes.store') }}" method="post" id="visitanteForm">
                        @csrf
                        <input type="hidden" name="source" value="{{ request('source', 'recepcion') }}">
                        
                        <!-- Datos del Visitante -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>  Datos del Visitante
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cedula" class="form-label">Cédula de Visitante <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="cedula" name="cedula" 
                                           maxlength="10" value="{{ old('cedula') }}" required
                                           placeholder="12345678" 
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                           pattern="[0-9]*" inputmode="numeric">
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">Escriba solo los números de cédula</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="primerNombre" class="form-label">Primer Nombre *</label>
                                <input type="text" class="form-control" id="primerNombre" name="primerNombre" 
                                       value="{{ old('primerNombre') }}" pattern="[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]+" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="segundoNombre" class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control" id="segundoNombre" name="segundoNombre" 
                                       value="{{ old('segundoNombre') }}" pattern="[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]+" >
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="primerApellido" class="form-label">Primer Apellido *</label>
                                <input type="text" class="form-control" id="primerApellido" name="primerApellido" 
                                       value="{{ old('primerApellido') }}" pattern="[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]+" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="segundoApellido" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="segundoApellido" name="segundoApellido" 
                                       value="{{ old('segundoApellido') }}" pattern="[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]+">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono *</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" 
                                       maxlength="11" value="{{ old('telefono') }}"  required>
                            </div>
                        </div>

                        <!-- Origen y Carnet -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary mb-3 mt-4">
                                    <i class="fas fa-map-marker-alt me-2"></i>  Origen y Carnet
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="institucion_de_origen" class="form-label">Institución de Origen</label>
                                <input type="text" class="form-control" id="institucion_de_origen" name="institucion_de_origen" 
                                       value="{{ old('institucion_de_origen') }}" placeholder="Ej: CANTV (Opcional)">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="codigo_carnet" class="form-label">Código de Carnet</label>
                                <select class="form-control" id="codigo_carnet" name="codigo_carnet">
                                    <option value="" disabled selected>Cargando carnets disponibles...</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="descripcion" class="form-label">Descripción/Motivo de la visita</label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" 
                                       value="{{ old('descripcion') }}" placeholder="Ej: Reunión de trabajo">
                            </div>
                        </div>

                        <!-- Fotografía -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary mb-3 mt-4">
                                    <i class="fas fa-camera me-2"></i>  Fotografía del Visitante
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <div id="camera-container" style="text-align:center;">
                                    <video id="video" width="320" height="240" autoplay playsinline 
                                           style="background:#222; border-radius: 8px;"></video>
                                    <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                                    <div id="preview-img" style="display:none;"></div>
                                    <br>
                                    <button type="button" id="capture-btn" class="btn btn-info mt-2">
                                        <i class="fas fa-camera me-1"></i> Tomar Foto
                                    </button>
                                    <button type="button" id="retry-btn" class="btn btn-warning mt-2" style="display:none;">
                                        <i class="fas fa-redo me-1"></i> Reintentar
                                    </button>
                                    <div class="mt-2">
                                        <label for="fileInput" class="btn btn-secondary">
                                            <i class="fas fa-upload me-1"></i> O Seleccionar Archivo
                                        </label>
                                    </div>
                                </div>
                                <!-- IMPORTANTE: NO usar display:none porque impide que el archivo se envíe en FormData -->
                                <input type="file" id="fileInput" name="foto_file" accept="image/*" 
                                       capture="environment" style="position: absolute; left: -9999px;">
                                <input type="hidden" id="fotoBase64" name="foto_base64">
                                <input type="hidden" id="url_img_existing" name="url_img_existing" value="">
                            </div>
                        </div>



                        <!-- Equipos -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary mb-3 mt-4">
                                    <i class="fas fa-laptop me-2"></i>  Equipos del Visitante
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="tipo_equipo_input" class="form-label">Tipo de Equipo</label>
                                <input type="text" class="form-control" id="tipo_equipo_input" 
                                       placeholder="Ej: Portátil, Tablet">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="serial_equipo_input" class="form-label">Serial del Equipo</label>
                                <input type="text" class="form-control" id="serial_equipo_input" 
                                       placeholder="Ej: SN12345">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="marca_input" class="form-label">Marca del Equipo</label>
                                <input type="text" class="form-control" id="marca_input" 
                                       placeholder="Ej: Dell, HP">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <button type="button" id="btnAgregarEquipo" class="btn btn-secondary">
                                    <i class="fas fa-plus me-1"></i>  Agregar Equipo
                                </button>
                                <small class="form-text text-muted">Máximo 5 equipos. Puede eliminar un equipo antes de enviar.</small>
                            </div>
                        </div>

                        <!-- Lista visible de equipos agregados -->
                        <div class="row">
                            <div class="col">
                                <ul id="listaEquipos" class="list-group mb-3"></ul>
                            </div>
                        </div>

                        <!-- Contenedor donde se añadirán inputs ocultos para enviar como arrays -->
                        <div id="equiposHiddenContainer"></div>

                        <!-- Empleado Responsable -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary mb-3 mt-4">
                                    <i class="fas fa-user-tie me-2"></i>  Empleado Responsable
                                </h5>
                            </div>
                        </div>

                        @php
                            $empleadoSeleccionado = session('selectedEmployee') ? json_decode(session('selectedEmployee'), true) : null;
                        @endphp

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cedula_empleado" class="form-label">Cédula de Trabajador *</label>
                                <input type="text" class="form-control" id="cedula_empleado" name="cedula_empleado" 
                                       value="{{ old('cedula_empleado', $empleadoSeleccionado['cedula'] ?? '') }}" readonly required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nombre_empleado" class="form-label">Nombre del Trabajador *</label>
                                <input type="text" class="form-control" id="nombre_empleado" name="nombre_empleado" 
                                       value="{{ old('nombre_empleado', $empleadoSeleccionado ? trim((is_string($v1 = $empleadoSeleccionado['primer_nombre'] ?? '') ? $v1 : '') . ' ' . (is_string($v2 = $empleadoSeleccionado['segundo_nombre'] ?? '') ? $v2 : '')) : '') }}" readonly required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="apellido_empleado" class="form-label">Apellido del Trabajador *</label>
                                <input type="text" class="form-control" id="apellido_empleado" name="apellido_empleado" 
                                       value="{{ old('apellido_empleado', $empleadoSeleccionado ? trim((is_string($va1 = $empleadoSeleccionado['primer_apellido'] ?? '') ? $va1 : '') . ' ' . (is_string($va2 = $empleadoSeleccionado['segundo_apellido'] ?? '') ? $va2 : '')) : '') }}" readonly required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ente" class="form-label">Ente del Trabajador</label>
                                <input type="text" class="form-control" id="ente" name="ente" 
                                       value="{{ old('ente', $empleadoSeleccionado['ente'] ?? 'MINCYT') }}" readonly>
                            </div>
                        </div>

                        @if(!$empleadoSeleccionado)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No se ha seleccionado un empleado responsable. 
                                <a href="{{ request('source') == 'vehicular' ? route('vehiculos.accesos') : route('recepcion.dashboard') }}" class="alert-link">Volver al dashboard</a> 
                                para seleccionar un empleado.
                            </div>
                        @endif

                        <div class="row mt-4">
                            <div class="col-12">
                                <center>
                                    <a class="btn btn-secondary me-2" href="{{ request('source') == 'vehicular' ? route('vehiculos.accesos') : route('recepcion.dashboard') }}">
                                        <i class="fas fa-arrow-left me-1"></i>  Volver al Dashboard
                                    </a>
                                    <a class="btn btn-danger me-2" href="{{ request('source') == 'vehicular' ? route('vehiculos.accesos') : route('recepcion.dashboard') }}">
                                        <i class="fas fa-times me-1"></i>  Cancelar
                                    </a>
                                    <button type="submit" id="submitBtn" class="btn btn-primary" {{ !$empleadoSeleccionado ? 'disabled' : '' }}>
                                        <i class="fas fa-save me-1"></i>  Registrar Visitante
                                    </button>
                                </center>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/camara.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // VALIDACIONES DE CAMPOS
    // ============================================
    
    // Validación para campos numéricos (Cédula y Teléfono)
    const cedulaInput = document.getElementById('cedula');
    const telefonoInput = document.getElementById('telefono');
    
    function validarSoloNumeros(event) {
        const input = event.target;
        // Eliminar cualquier carácter que no sea número
        input.value = input.value.replace(/[^0-9]/g, '');
    }
    
    // Aplicar validación en tiempo real
    cedulaInput.addEventListener('input', validarSoloNumeros);
    telefonoInput.addEventListener('input', validarSoloNumeros);
    
    // Validación para campos alfabéticos (Nombres y Apellidos)
    const primerNombreInput = document.getElementById('primerNombre');
    const segundoNombreInput = document.getElementById('segundoNombre');
    const primerApellidoInput = document.getElementById('primerApellido');
    const segundoApellidoInput = document.getElementById('segundoApellido');
    
    function validarSoloLetras(event) {
        const input = event.target;
        // Permitir solo letras (incluyendo acentos, ñ, ü) y espacios
        input.value = input.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]/g, '');
    }
    
    // Aplicar validación en tiempo real a todos los campos de nombres
    primerNombreInput.addEventListener('input', validarSoloLetras);
    segundoNombreInput.addEventListener('input', validarSoloLetras);
    primerApellidoInput.addEventListener('input', validarSoloLetras);
    segundoApellidoInput.addEventListener('input', validarSoloLetras);
    
    // Cargar carnets disponibles
    fetch('/proxy/carnets-disponibles', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('codigo_carnet');
        select.innerHTML = '<option value="" selected>Seleccione un carnet (Opcional)</option>';
        if (data.success && data.data && data.data.length > 0) {
            data.data.forEach(carnet => {
                select.innerHTML += `<option value="${carnet.id}">Carnet #${carnet.codigo || carnet.id}</option>`;
            });
        } else {
            select.innerHTML = '<option value="" selected>No hay carnets disponibles</option>';
        }
    })
    .catch(error => {
        console.error('Error al cargar carnets:', error);
        document.getElementById('codigo_carnet').innerHTML = '<option value="" selected>Error al cargar carnets</option>';
    });
    
    // ============================================
    // BÚSQUEDA SAIME Y VALIDACIÓN DE VISITANTE
    // ============================================
    
    cedulaInput.addEventListener('blur', function() {
        const cedula = this.value.trim();
        if (cedula.length >= 7) {
            buscarVisitanteLocal(cedula);
        }
    });

    async function buscarVisitanteLocal(cedula) {
        const notif = document.getElementById('notifVisitante');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        try {
            // La validación de visitante dentro se maneja en el backend
            
            // 2. Buscar en base de datos local
            notif.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Buscando en registros anteriores...</div>';
            
            const localResponse = await fetch('/proxy/buscar-visitante-cedula', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ cedula: cedula })
            });
            
            const localData = await localResponse.json();
            
            // Verificar si el visitante está actualmente dentro
            if (localData.found && localData.dentro) {
                notif.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡VISITANTE YA EN TORRE!</strong><br>
                        ${localData.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                // Deshabilitar el formulario
                const submitBtn = document.getElementById('submitBtn');
                const form = document.getElementById('visitanteForm');
                
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-ban me-1"></i>Visitante en Torre - No Disponible';
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-danger');
                }
                
                // Deshabilitar campos del formulario
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (input.name !== 'cedula') { // Mantener cédula habilitada para poder cambiar
                        input.disabled = true;
                    }
                });
                
                return;
            }
            
            if (localData.success && localData.found && localData.data) {
                // Llenar campos con datos locales
                document.getElementById('primerNombre').value = localData.data.primer_nombre || '';
                document.getElementById('segundoNombre').value = localData.data.segundo_nombre || '';
                document.getElementById('primerApellido').value = localData.data.primer_apellido || '';
                document.getElementById('segundoApellido').value = localData.data.segundo_apellido || '';
                document.getElementById('telefono').value = localData.data.telefono || '';
                
                notif.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check me-2"></i>
                        <strong>Visitante Recurrente Detectado</strong><br>
                        Datos encontrados en registros anteriores. Los campos se han llenado automáticamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                setTimeout(() => {
                    notif.innerHTML = '';
                }, 8000);
                return;
            }
            
            // 3. Si no está en local, buscar en SAIME
            buscarEnSaime(cedula);
            
        } catch (error) {
            console.error('Error en búsqueda local:', error);
            // Si falla la búsqueda local, intentar SAIME
            buscarEnSaime(cedula);
        }
    }

    function buscarEnSaime(cedula) {
        // Mostrar loading
        const notif = document.getElementById('notifVisitante');
        notif.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Consultando datos en SAIME...</div>';

        fetch(`/proxy/buscar-cedula?cedula=${encodeURIComponent(cedula)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('SAIME Response:', data);
                
                if (data.success && data.data) {
                    // Llenar campos con datos de SAIME
                    document.getElementById('primerNombre').value = data.data.primer_nombre || '';
                    document.getElementById('segundoNombre').value = data.data.segundo_nombre || '';
                    document.getElementById('primerApellido').value = data.data.primer_apellido || '';
                    document.getElementById('segundoApellido').value = data.data.segundo_apellido || '';
                    
                    const source = data.source === 'local' ? 'base de datos local' : 'SAIME';
                    notif.innerHTML = `<div class="alert alert-success"><i class="fas fa-check me-2"></i>Datos encontrados en ${source}</div>`;
                } else {
                    const message = data.message || 'No se encontraron datos en SAIME';
                    notif.innerHTML = `<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>${message}. Complete manualmente.</div>`;
                }
                
                // Limpiar notificación después de 5 segundos
                setTimeout(() => {
                    notif.innerHTML = '';
                }, 5000);
            })
            .catch(error => {
                console.error('Error SAIME:', error);
                notif.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error al consultar SAIME: ${error.message}. Complete manualmente.</div>`;
                setTimeout(() => {
                    notif.innerHTML = '';
                }, 5000);
            });
    }

    // Rehabilitar formulario cuando se modifique la cédula
    cedulaInput.addEventListener('input', function() {
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('visitanteForm');
        
        // Rehabilitar botón
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Registrar Visitante';
            submitBtn.classList.remove('btn-danger');
            submitBtn.classList.add('btn-primary');
        }
        
        // Rehabilitar todos los campos del formulario
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = false;
        });
        
        // Limpiar mensajes previos
        const notif = document.getElementById('notifVisitante');
        if (notif) notif.innerHTML = '';
    });

    // Manejar cambio de piso para cargar carnets
    const pisoSelect = document.getElementById('piso');
    const carnetSelect = document.getElementById('codigo_carnet');

    if (pisoSelect) {
        pisoSelect.addEventListener('change', function() {
            const piso = this.value;
            
            if (!piso) {
                carnetSelect.innerHTML = '<option value="" disabled>Seleccione un piso primero</option>';
                carnetSelect.disabled = true;
                return;
            }

            carnetSelect.innerHTML = '<option value="" disabled>Cargando carnets...</option>';
            carnetSelect.disabled = true;

            fetch(`/api/carnets-disponibles?piso=${encodeURIComponent(piso)}`)
                .then(response => response.json())
                .then(data => {
                    carnetSelect.innerHTML = '<option value="" disabled>Seleccione un carnet (opcional)</option>';
                    
                    if (data.success && data.data.length > 0) {
                        data.data.forEach(carnet => {
                            const option = document.createElement('option');
                            option.value = carnet.id;
                            option.textContent = `Carnet ${carnet.id} - Piso ${carnet.piso_asociado}`;
                            carnetSelect.appendChild(option);
                        });
                        carnetSelect.disabled = false;
                    } else {
                        carnetSelect.innerHTML = '<option value="" disabled>No hay carnets disponibles para este piso</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    carnetSelect.innerHTML = '<option value="" disabled>Error al cargar carnets</option>';
                });
        });
    }

    // Gestión de equipos
    let equiposCount = 0;
    const maxEquipos = 5;

    document.getElementById('btnAgregarEquipo').addEventListener('click', function() {
        if (equiposCount >= maxEquipos) {
            alert('Máximo 5 equipos permitidos');
            return;
        }

        const tipo = document.getElementById('tipo_equipo_input').value.trim();
        const serial = document.getElementById('serial_equipo_input').value.trim();
        const marca = document.getElementById('marca_input').value.trim();

        if (!tipo) {
            alert('El tipo de equipo es obligatorio');
            return;
        }

        agregarEquipo(tipo, serial, marca);
        
        // Limpiar campos
        document.getElementById('tipo_equipo_input').value = '';
        document.getElementById('serial_equipo_input').value = '';
        document.getElementById('marca_input').value = '';
    });

    function agregarEquipo(tipo, serial, marca) {
        equiposCount++;
        
        // Agregar a la lista visible
        const lista = document.getElementById('listaEquipos');
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `
            <div>
                <strong>${tipo}</strong><br>
                <small class="text-muted">Serial: ${serial || 'N/A'} | Marca: ${marca || 'N/A'}</small>
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarEquipo(this, ${equiposCount})">
                <i class="fas fa-trash"></i>
            </button>
        `;
        lista.appendChild(li);

        // Agregar inputs ocultos
        const container = document.getElementById('equiposHiddenContainer');
        container.innerHTML += `
            <input type="hidden" name="tipo_equipo[]" value="${tipo}" data-equipo="${equiposCount}">
            <input type="hidden" name="serial_equipo[]" value="${serial}" data-equipo="${equiposCount}">
            <input type="hidden" name="marca[]" value="${marca}" data-equipo="${equiposCount}">
        `;
    }

    window.eliminarEquipo = function(button, equipoId) {
        // Eliminar de la lista visible
        button.closest('li').remove();
        
        // Eliminar inputs ocultos
        const inputs = document.querySelectorAll(`input[data-equipo="${equipoId}"]`);
        inputs.forEach(input => input.remove());
        
        equiposCount--;
    };

    // Ya no existe checkbox de vehículo porque ahora es obligatorio si viene de source != recepcion
    // y no aparece si viene de source = recepcion.
    const placaInput = document.getElementById('placa_vehiculo');
    const tipoInput = document.getElementById('tipo_vehiculo');
    const colorInput = document.getElementById('color_vehiculo');
    const marcaInput = document.getElementById('marca_vehiculo');
    const modeloInput = document.getElementById('modelo_vehiculo');

    // Validación de placa en tiempo real para visitantes
    if (placaInput) {
        placaInput.addEventListener('blur', function() {
            const placa = this.value.trim();
            if (placa.length > 0) {
                fetch(`/api/vehiculos/check-placa?placa=${encodeURIComponent(placa)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.inside) {
                            // Mostrar sweetalert
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Acceso Denegado',
                                    text: 'Este Vehiculo ya se encuentra dentro del estacionamiento',
                                    icon: 'error'
                                });
                            } else {
                                alert('Este Vehiculo ya se encuentra dentro del estacionamiento');
                            }
                            // Limpiar input para forzar otra placa
                            this.value = '';
                        }
                    })
                    .catch(err => console.error('Error al verificar placa:', err));
            }
        });
    }

    // Manejar envío del formulario usando AJAX para mejor compatibilidad
    const visitanteForm = document.getElementById('visitanteForm');
    if (visitanteForm) {
        visitanteForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Siempre prevenir submit por defecto
            
            const submitBtn = document.getElementById('submitBtn');
            const notif = document.getElementById('notifVisitante');
            
            // Validaciones finales antes de enviar
            let errores = [];
            
            // Validar cédula (solo números)
            const cedula = cedulaInput.value.trim();
            if (!/^[0-9]+$/.test(cedula)) {
                errores.push('La cédula debe contener solo números');
            }
            
            // Validar teléfono (solo números)
            const telefono = telefonoInput.value.trim();
            if (!/^[0-9]+$/.test(telefono)) {
                errores.push('El teléfono debe contener solo números');
            }
            
            // Validar primer nombre (solo letras)
            const primerNombre = primerNombreInput.value.trim();
            if (primerNombre && !/^[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(primerNombre)) {
                errores.push('El primer nombre debe contener solo letras');
            }
            
            // Validar segundo nombre (solo letras, si está lleno)
            const segundoNombre = segundoNombreInput.value.trim();
            if (segundoNombre && !/^[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(segundoNombre)) {
                errores.push('El segundo nombre debe contener solo letras');
            }
            
            // Validar primer apellido (solo letras)
            const primerApellido = primerApellidoInput.value.trim();
            if (primerApellido && !/^[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(primerApellido)) {
                errores.push('El primer apellido debe contener solo letras');
            }
            
            // Validar segundo apellido (solo letras, si está lleno)
            const segundoApellido = segundoApellidoInput.value.trim();
            if (segundoApellido && !/^[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(segundoApellido)) {
                errores.push('El segundo apellido debe contener solo letras');
            }

            
            // Si hay errores, mostrar mensajes y detener
            if (errores.length > 0) {
                let errorHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                errorHTML += '<strong><i class="fas fa-exclamation-triangle me-2"></i>Por favor corrija los siguientes errores:</strong><ul class="mb-0 mt-2">';
                errores.forEach(error => {
                    errorHTML += `<li>${error}</li>`;
                });
                errorHTML += '</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                
                if (notif) {
                    notif.innerHTML = errorHTML;
                }
                
                // Scroll hacia arriba para ver los errores
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return false;
            }
            
            // Mostrar loading
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Registrando visitante...';
            }
            
            if (notif) {
                notif.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-spinner fa-spin me-2"></i>
                        Procesando registro del visitante...
                    </div>
                `;
            }
            
            // Crear FormData desde el formulario
            const formData = new FormData(visitanteForm);
            
            // CRÍTICO: Agregar manualmente el archivo si existe
            // Algunos navegadores no incluyen archivos de inputs ocultos en FormData automáticamente
            const fileInputDirect = document.getElementById('fileInput');
            if (fileInputDirect && fileInputDirect.files.length > 0) {
                console.log('🔧 Agregando archivo manualmente a FormData');
                // Remover el valor anterior si existe
                formData.delete('foto_file');
                // Agregar el archivo manualmente
                formData.append('foto_file', fileInputDirect.files[0]);
                console.log('✅ Archivo agregado manualmente:', {
                    nombre: fileInputDirect.files[0].name,
                    tamaño: fileInputDirect.files[0].size
                });
            }
            
            
            // Limpiar campos vacíos para evitar problemas
            if (!formData.get('foto_base64')) {
                formData.delete('foto_base64');
            }
            if (!formData.get('url_img_existing')) {
                formData.delete('url_img_existing');
            }
            
            // Log detallado para debugging
            console.log('=== ENVÍO AJAX DEL FORMULARIO (XHR) ===');
            console.log('📋 Revisando inputs antes del envío:');
            
            // Verificar qué está en FormData
            console.log('  📦 Contenido de FormData final:');
            console.log('    - foto_file en FormData:', formData.get('foto_file') ? 'Sí' : 'No');
            if (formData.get('foto_file')) {
                console.log('    - foto_file nombre:', formData.get('foto_file').name);
                console.log('    - foto_file tamaño:', formData.get('foto_file').size, 'bytes');
            }
            
            // Usar XMLHttpRequest en lugar de fetch para mayor robustez con archivos
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("visitantes.store") }}', true);
            
            // Headers necesarios
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.setRequestHeader('Accept', 'application/json');
            // IMPORTANTE: NO establecer Content-Type, dejar que el navegador lo haga con el boundary
            
            xhr.onload = function() {
                let response = null;
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (e) {
                    // Si no es JSON, puede ser un error HTML o un redirect
                    console.log('Respuesta no es JSON:', xhr.responseText.substring(0, 100));
                }
                
                if (xhr.status >= 200 && xhr.status < 300) {
                    // Success
                    if (response && response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        // Fallback - Ir al dashboard correcto
                        @if(request('source') == 'vehicular')
                            window.location.href = '{{ route("vehiculos.accesos") }}';
                        @else
                            window.location.href = '{{ route("recepcion.dashboard") }}';
                        @endif
                    }
                } else {
                    // Error
                    console.error('Error del servidor:', response || xhr.responseText);
                    
                    let errorMessage = 'Error al registrar el visitante.';
                    
                    if (response) {
                        if (response.message) {
                            errorMessage = response.message;
                        } else if (response.errors) {
                            errorMessage = '<ul class="mb-0">';
                            Object.values(response.errors).forEach(errArray => {
                                errArray.forEach(err => {
                                    errorMessage += `<li>${err}</li>`;
                                });
                            });
                            errorMessage += '</ul>';
                        }
                    }
                    
                    if (notif) {
                        notif.innerHTML = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle me-2"></i>Error (${xhr.status}):</strong><br>
                                ${errorMessage}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                    }
                    
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Registrar Visitante';
                    }
                    
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            };
            
            xhr.onerror = function() {
                console.error('Error de red al intentar conectar');
                if (notif) {
                    notif.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Error de Conexión:</strong><br>
                            No se pudo conectar con el servidor. Por favor verifique su conexión e intente nuevamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                }
                
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Registrar Visitante';
                }
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };
            
            xhr.send(formData);
            
            return false;

            
            return false;
        });
    }

    // Función para mostrar notificaciones mejoradas
    window.showNotification = function(message, type = 'info', duration = 5000) {
        const notif = document.getElementById('notifVisitante');
        if (!notif) return;
        
        const icons = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-triangle',
            'warning': 'fas fa-exclamation-circle',
            'info': 'fas fa-info-circle'
        };
        
        const colors = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };
        
        notif.innerHTML = `
            <div class="alert ${colors[type]} alert-dismissible fade show" role="alert">
                <i class="${icons[type]} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Auto-ocultar después del tiempo especificado
        if (duration > 0) {
            setTimeout(() => {
                const alert = notif.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => {
                        notif.innerHTML = '';
                    }, 300);
                }
            }, duration);
        }
    };

    // El JavaScript de la cámara está en js/camara.js
});
</script>
@endpush
@endsection