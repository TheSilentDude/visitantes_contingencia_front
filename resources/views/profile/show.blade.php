@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800">
                    <i class="fas fa-user-circle me-2"></i>Mi Perfil
                </h1>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <img id="avatarPreview" 
                             src="{{ $user->avatar ? asset($user->avatar) : asset('img/avatar-placeholder.svg') }}" 
                             alt="Avatar" 
                             class="rounded-circle" 
                             style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #4e73df;">
                    </div>
                    @if($user->empleado)
                        <h4 class="mb-1">{{ $user->empleado->primer_nombre }} {{ $user->empleado->primer_apellido }}</h4>
                    @endif
                    <p class="text-muted mb-1">{{ '@' . $user->usuario }}</p>
                    @if($user->empleado)
                        <p class="text-muted mb-3">{{ $user->empleado->correo }}</p>
                    @endif
                    @php
                        $rolNombre = $user->rol->rol ?? $user->rol->descripcion ?? session('user_rol', 'Usuario');
                    @endphp
                    <span class="badge bg-primary">{{ ucfirst($rolNombre) }}</span>
                    <hr class="my-4">
                    <div class="text-start">
                        @if($user->empleado)
                            <p class="mb-2"><i class="fas fa-id-card text-primary me-2"></i> {{ $user->empleado->origen ?? 'V' }}-{{ $user->empleado->cedula }}</p>
                            <p class="mb-2"><i class="fas fa-phone text-primary me-2"></i> {{ $user->empleado->telefono ?? 'No especificado' }}</p>
                            <p class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> {{ $user->empleado->direccion ? Str::limit($user->empleado->direccion, 30) : 'No especificada' }}</p>
                        @endif
                        <p class="mb-2"><i class="fas fa-calendar text-primary me-2"></i> Miembro desde {{ \Illuminate\Support\Carbon::parse($user->created_at)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for Profile Settings -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">
                                <i class="fas fa-user me-1"></i> Información Personal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab">
                                <i class="fas fa-lock me-1"></i> Cambiar Contraseña
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="profileTabContent">
                        <!-- Personal Information Tab -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            @if($user->empleado)
                                <form id="profileForm" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <h6 class="text-muted mb-3"><i class="fas fa-user-tag me-1"></i> Datos Personales (Solo lectura)</h6>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Los nombres, apellidos y cédula son <strong>solo lectura</strong>. Contacte al administrador para cambios.
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Primer Nombre</label>
                                            <input type="text" class="form-control" value="{{ $user->empleado->primer_nombre }}" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Segundo Nombre</label>
                                            <input type="text" class="form-control" value="{{ $user->empleado->segundo_nombre }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Primer Apellido</label>
                                            <input type="text" class="form-control" value="{{ $user->empleado->primer_apellido }}" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Segundo Apellido</label>
                                            <input type="text" class="form-control" value="{{ $user->empleado->segundo_apellido }}" readonly>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <label class="form-label">Cédula de Identidad</label>
                                            <input type="text" class="form-control" value="{{ ($user->empleado->origen ?? 'V') . '-' . $user->empleado->cedula }}" readonly>
                                        </div>
                                    </div>

                                    <hr>
                                    <h6 class="text-muted mb-3 mt-4"><i class="fas fa-edit me-1"></i> Datos Editables</h6>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nombre de Usuario *</label>
                                            <input type="text" name="usuario" class="form-control" value="{{ $user->usuario }}" required>
                                            <small class="text-muted">Se usa para iniciar sesión</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Correo Electrónico *</label>
                                            <input type="email" name="email" class="form-control" value="{{ $user->empleado->correo }}" required>
                                            <small class="text-muted">También se usa para iniciar sesión</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" name="telefono" class="form-control" value="{{ $user->empleado->telefono }}" placeholder="0414-1234567">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Avatar / Foto de Perfil</label>
                                            <input type="file" name="avatar" id="avatarInput" class="form-control" accept="image/*">
                                            <small class="text-muted">JPG, PNG, GIF (Máx. 2MB)</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Dirección</label>
                                            <textarea name="direccion" class="form-control" rows="2" placeholder="Ej: Av. Principal, Edificio X, Piso Y">{{ $user->empleado->direccion }}</textarea>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 mt-4">
                                        <button type="submit" class="btn btn-success btn-lg px-4">
                                            <i class="fas fa-check-circle me-2"></i> Guardar Cambios
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary btn-lg px-4">
                                            <i class="fas fa-undo me-2"></i> Descartar Cambios
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    No hay un empleado asociado a esta cuenta de usuario. Contacte al administrador.
                                </div>
                            @endif
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <form id="passwordForm">
                                @csrf
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>La contraseña debe contener:</strong>
                                </div>

                                <div class="mb-3" id="passwordRequirements">
                                    <ul class="list-unstyled mb-0">
                                        <li id="req-minuscule"><i class="fas fa-times-circle text-danger me-2"></i>Al menos una letra minúscula</li>
                                        <li id="req-mayuscule"><i class="fas fa-times-circle text-danger me-2"></i>Al menos una letra mayúscula</li>
                                        <li id="req-number"><i class="fas fa-times-circle text-danger me-2"></i>Al menos un número</li>
                                        <li id="req-special"><i class="fas fa-times-circle text-danger me-2"></i>Al menos un carácter especial (.,$#)</li>
                                        <li id="req-length"><i class="fas fa-times-circle text-danger me-2"></i>Min 8 y Max 16 caracteres</li>
                                    </ul>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Contraseña Actual *</label>
                                        <div class="input-group">
                                            <input type="password" name="current_password" id="current_password" class="form-control" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                <i class="fas fa-eye" id="current_password_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nueva Contraseña *</label>
                                        <div class="input-group">
                                            <input type="password" name="new_password" id="new_password" class="form-control" required minlength="8" maxlength="16">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                                <i class="fas fa-eye" id="new_password_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirmar Nueva Contraseña *</label>
                                        <div class="input-group">
                                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required minlength="8" maxlength="16">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                                <i class="fas fa-eye" id="new_password_confirmation_icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-4">
                                        <i class="fas fa-key me-2"></i> Cambiar Contraseña
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-lg px-4" onclick="document.getElementById('passwordForm').reset(); resetPasswordRequirements();">
                                        <i class="fas fa-times-circle me-2"></i> Limpiar Formulario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Profile Update
document.getElementById('profileForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('_method', 'PUT'); // RESTful method spoofing
    
    fetch('{{ route("profile.update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async function(response) {
        const text = await response.text();
        let result = {};
        try {
            result = text ? JSON.parse(text) : {};
        } catch (e) {
            console.error('Respuesta no JSON:', text.substring(0, 200));
            alert('Error al actualizar el perfil: el servidor no devolvió JSON (¿API backend activa y ruta /api/profile?).');
            return;
        }
        if (!response.ok || result.success === false) {
            var msg = result.message || '';
            if (result.errors && typeof result.errors === 'object') {
                msg = msg || Object.values(result.errors).flat().join('\n');
            }
            alert('✗ ' + (msg || 'Error al actualizar el perfil'));
            return;
        }
        if (result.success) {
            alert('✓ ' + (result.message || 'Perfil actualizado'));
            window.location.reload();
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        alert('Error al actualizar el perfil');
    });
});

// Password Update
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();

    var passwordFormEl = this;

    console.log('Password form submitted');

    const formData = new FormData(passwordFormEl);
    const data = Object.fromEntries(formData);

    console.log('Sending password update request:', data);

    fetch('{{ route("profile.password") }}', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(async function(response) {
        const text = await response.text();
        let result = {};
        try {
            result = text ? JSON.parse(text) : {};
        } catch (e) {
            console.error('Respuesta no JSON:', text.substring(0, 200));
            alert('Error al cambiar la contraseña: respuesta inválida del servidor.');
            return;
        }
        if (!response.ok || result.success === false) {
            var msg = result.message || '';
            if (result.errors && typeof result.errors === 'object') {
                msg = msg || Object.values(result.errors).flat().join('\n');
            }
            alert('✗ ' + (msg || 'Error al cambiar la contraseña'));
            return;
        }
        if (result.success) {
            alert('✓ ' + (result.message || 'Contraseña actualizada'));
            passwordFormEl.reset();
        }
    })
    .catch(function(error) {
        console.error('Error completo:', error);
        alert('Error al cambiar la contraseña: ' + error.message);
    });
});

// Avatar Preview
document.getElementById('avatarInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Reset password requirements
function resetPasswordRequirements() {
    const requirements = ['req-minuscule', 'req-mayuscule', 'req-number', 'req-special', 'req-length'];
    requirements.forEach(req => {
        const elem = document.getElementById(req);
        const icon = elem.querySelector('i');
        icon.className = 'fas fa-times-circle text-danger me-2';
    });
}

// Validate password requirements in real-time
document.getElementById('new_password')?.addEventListener('input', function(e) {
    const password = e.target.value;
    
    // Check minuscule
    const hasMinuscule = /[a-z]/.test(password);
    updateRequirement('req-minuscule', hasMinuscule);
    
    // Check mayuscule
    const hasMayuscule = /[A-Z]/.test(password);
    updateRequirement('req-mayuscule', hasMayuscule);
    
    // Check number
    const hasNumber = /\d/.test(password);
    updateRequirement('req-number', hasNumber);
    
    // Check special character
    const hasSpecial = /[@$!%*?&#.]/.test(password);
    updateRequirement('req-special', hasSpecial);
    
    // Check length
    const hasLength = password.length >= 8 && password.length <= 16;
    updateRequirement('req-length', hasLength);
});

function updateRequirement(reqId, isValid) {
    const elem = document.getElementById(reqId);
    const icon = elem.querySelector('i');
    
    if (isValid) {
        icon.className = 'fas fa-check-circle text-success me-2';
    } else {
        icon.className = 'fas fa-times-circle text-danger me-2';
    }
}
</script>
@endpush
@endsection
