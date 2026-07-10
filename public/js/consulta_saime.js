






async function consultarSaime() {
    const cedulaInput = document.getElementById('cedula');
    const cedula = cedulaInput.value.trim();
    
    // Validación básica
    if (!cedula || !/^\d{5,9}$/.test(cedula)) {
        mostrarError('Cédula inválida. Debe tener entre 5 y 9 dígitos');
        return;
    }

    // Mostrar estado de carga
    cedulaInput.classList.add('loading');
    
    try {
        const response = await fetch('consultar_saime.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ cedula: cedula })
        });

        // Verificar si la respuesta está vacía
        const responseText = await response.text();
        if (!responseText) {
            throw {
                type: 'empty_response',
                message: 'El servidor no respondió'
            };
        }

        const data = JSON.parse(responseText);
        
        if (!response.ok) {
            // Aquí podemos identificar exactamente qué falló
            let errorMessage = data.message || `Error HTTP: ${response.status}`;
            
            if (data.error_type) {
                switch(data.error_type) {
                    case 'token_request':
                        errorMessage = 'Fallo al conectar con el servidor de autenticación';
                        break;
                    case 'token_response':
                        errorMessage = 'Error al obtener credenciales de acceso';
                        break;
                    case 'saime_connection':
                        errorMessage = 'No se pudo conectar con el SAIME';
                        break;
                    case 'saime_response':
                        errorMessage = 'El SAIME respondió con un error';
                        break;
                    case 'json_parsing':
                        errorMessage = 'Error al interpretar los datos del SAIME';
                        break;
                    case 'no_data':
                        errorMessage = 'No se encontraron datos para esta cédula';
                        break;
                    default:
                        errorMessage = `Error: ${data.message}`;
                }
            }
            
            throw {
                type: data.error_type || 'unknown_error',
                message: errorMessage,
                details: data
            };
        }
        
        if (!data.success) {
            throw {
                type: 'api_error',
                message: data.message || 'Error en la consulta',
                details: data
            };
        }

        // Procesar datos exitosos
        const saimeEntry = data.data;
        console.log('Datos SAIME obtenidos:', saimeEntry);
        autocompletarCampos({
            primer_nombre: saimeEntry.primer_nombre || '',
            segundo_nombre: saimeEntry.segundo_nombre || '',
            primer_apellido: saimeEntry.primer_apellido || '',
            segundo_apellido: saimeEntry.segundo_apellido || ''
        });
        
    } catch (error) {
        console.error('Error en consulta SAIME:', error);
        
        // Mensajes personalizados según el tipo de error
        let mensajeUsuario = 'No se pudieron obtener los datos automáticamente. ';
        
        if (error.type) {
            switch(error.type) {
                case 'token_request':
                    mensajeUsuario += 'Problema de conexión con el servidor de autenticación.';
                    break;
                case 'token_response':
                    mensajeUsuario += 'Error al obtener credenciales de acceso.'; 
                    break;
                case 'saime_connection':
                    mensajeUsuario += 'No se pudo conectar con el sistema SAIME.';
                    break;
                case 'saime_response':
                    mensajeUsuario += 'El sistema SAIME respondió con un error.';
                    break;
                case 'json_parsing':
                    mensajeUsuario += 'Error al interpretar los datos recibidos.';
                    break;
                case 'no_data':
                    mensajeUsuario += 'No se encontraron datos para esta cédula. Rellene manualmente el visitante.';
                    break;
                default:
                    mensajeUsuario += error.message || 'Error desconocido.';
            }
        } else {
            mensajeUsuario += error.message || 'Error desconocido.';
        }
        
        mostrarError(mensajeUsuario);
    } finally {
        cedulaInput.classList.remove('loading');
    }
}

function autocompletarCampos(data) {
    if (data.primer_nombre) {
        document.getElementById('primerNombre').value = data.primer_nombre;
    }

    if (data.segundo_nombre) {
        document.getElementById('segundoNombre').value = data.segundo_nombre;
    }
    
    if (data.primer_apellido) {
        document.getElementById('primerApellido').value = data.primer_apellido;
    }

    if (data.segundo_apellido) {
        document.getElementById('segundoApellido').value = data.segundo_apellido;
    }
    
    if (data.fecha_nacimiento) {
        const fecha = formatearFecha(data.fecha_nacimiento);
        if (fecha) {
            document.getElementById('fecha_nacimiento').value = fecha;
        }
    }
    
    if (data.sexo) {
        if (data.sexo === 'M') {
            document.getElementById('sexo').value = 'Masculino';
        } else if (data.sexo === 'F') {
            document.getElementById('sexo').value = 'Femenino';
        }
    }
}

function formatearFecha(fechaStr) {
  const date = new Date(fechaStr);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  
  return `${year}-${month}-${day}`;
}

function mostrarError(mensaje) {
    // Implementa tu lógica para mostrar errores al usuario
    console.error(mensaje);
    alert(mensaje);
}
// Navegación entre secciones mejorada

  
  

/*function mostrarToast(mensaje) {
  const toast = document.getElementById('toast');
  toast.textContent = mensaje;
  toast.style.display = 'block';
  setTimeout(() => {
    toast.style.display = 'none';
  }, 5000);
}*/
// Llamar la función cuando el campo de cédula pierde el foco
document.getElementById('cedula').addEventListener('blur', consultarSaime);
