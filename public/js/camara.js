// Gestión de cámara para captura de fotos de visitantes
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('capture-btn');
    const retryBtn = document.getElementById('retry-btn');
    const previewImg = document.getElementById('preview-img');
    const fotoBase64Input = document.getElementById('fotoBase64');

    let stream = null;
    let photoTaken = false;

    // Inicializar cámara
    async function initCamera() {
        try {
            // Verificar si el navegador soporta getUserMedia
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('getUserMedia no está soportado en este navegador');
            }

            // Solicitar acceso a la cámara
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 320 },
                    height: { ideal: 240 },
                    facingMode: 'user' // Cámara frontal preferida
                }
            });

            video.srcObject = stream;
            video.style.display = 'block';
            captureBtn.style.display = 'inline-block';

        } catch (error) {
            console.error('Error accessing camera:', error);

            // Mostrar mensaje de error y opción de subir archivo
            video.style.display = 'none';
            captureBtn.style.display = 'none';

            const cameraContainer = document.getElementById('camera-container');
            if (cameraContainer) {
                // Crear alerta
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning';
                alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>No se pudo acceder a la cámara. Puede subir una foto desde su dispositivo.';

                // Insertar alerta al principio del contenedor
                cameraContainer.insertBefore(alertDiv, cameraContainer.firstChild);
            }
        }
    }

    // Capturar foto
    function capturePhoto() {
        if (!stream) {
            console.error('❌ No hay stream de cámara disponible');
            alert('Error: No se pudo acceder a la cámara. Por favor recargue la página e intente nuevamente.');
            return;
        }

        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        console.log('📸 === CAPTURANDO FOTO ===');
        console.log('Video dimensions:', {
            videoWidth: video.videoWidth,
            videoHeight: video.videoHeight,
            canvasWidth: canvas.width,
            canvasHeight: canvas.height
        });

        // Dibujar el frame actual del video en el canvas
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convertir a base64
        const dataURL = canvas.toDataURL('image/jpeg', 0.8);

        console.log('🖼️  Imagen capturada:');
        console.log('  - Longitud total:', dataURL.length, 'caracteres');
        console.log('  - Inicia correctamente:', dataURL.startsWith('data:image/jpeg;base64,'));
        console.log('  - Primeros 50 chars:', dataURL.substring(0, 50));

        // Guardar en input oculto
        if (fotoBase64Input) {
            console.log('✅ Input fotoBase64 encontrado');
            console.log('  - ID del input:', fotoBase64Input.id);
            console.log('  - Name del input:', fotoBase64Input.name);
            console.log('  - Valor actual antes:', fotoBase64Input.value.length, 'chars');

            fotoBase64Input.value = dataURL;

            console.log('  - Valor actual después:', fotoBase64Input.value.length, 'chars');
            console.log('  - Valores coinciden:', fotoBase64Input.value === dataURL);

            // Verificar que se mantenga después de un pequeño delay
            setTimeout(() => {
                console.log('⏱️  Verificación después de 100ms:');
                console.log('  - Valor sigue presente:', fotoBase64Input.value.length, 'chars');
            }, 100);

            console.log('✅ Imagen guardada en input oculto correctamente');
        } else {
            console.error('❌ ERROR: Input fotoBase64 NO encontrado en el DOM');
            console.error('Inputs con name="foto_base64":', document.querySelectorAll('input[name="foto_base64"]').length);
            console.error('Inputs con id="fotoBase64":', document.querySelectorAll('#fotoBase64').length);
        }

        // Mostrar preview
        previewImg.innerHTML = `
            <img src="${dataURL}" alt="Foto capturada" class="img-thumbnail" style="max-width: 320px; max-height: 240px;">
            <br><small class="text-success"><i class="fas fa-check me-1"></i>Foto capturada correctamente (${Math.round(dataURL.length / 1024)} KB)</small>
        `;
        previewImg.style.display = 'block';

        // Ocultar video y mostrar botón de reintentar
        video.style.display = 'none';
        captureBtn.style.display = 'none';
        retryBtn.style.display = 'inline-block';

        photoTaken = true;

        console.log('📸 === FIN CAPTURA ===');
    }

    // Reintentar captura
    function retryCapture() {
        // Limpiar datos anteriores
        fotoBase64Input.value = '';
        previewImg.style.display = 'none';
        previewImg.innerHTML = '';

        // Mostrar video nuevamente
        video.style.display = 'block';
        captureBtn.style.display = 'inline-block';
        retryBtn.style.display = 'none';

        photoTaken = false;
    }

    // Event listeners
    if (captureBtn) {
        captureBtn.addEventListener('click', capturePhoto);
    }

    if (retryBtn) {
        retryBtn.addEventListener('click', retryCapture);
    }

    // Inicializar cámara al cargar la página
    initCamera();

    // Limpiar stream al salir de la página
    window.addEventListener('beforeunload', function () {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });

    // Manejar selección de archivo
    const fileInputElement = document.getElementById('fileInput');
    if (fileInputElement) {
        fileInputElement.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                console.log('📁 Archivo seleccionado:', {
                    nombre: file.name,
                    tamaño: file.size,
                    tipo: file.type
                });

                // Mostrar preview del archivo
                const reader = new FileReader();
                reader.onload = function (event) {
                    if (previewImg) {
                        previewImg.innerHTML = `
                            <img src="${event.target.result}" alt="Imagen seleccionada" class="img-thumbnail" style="max-width: 320px; max-height: 240px;">
                            <br><small class="text-success"><i class="fas fa-check me-1"></i>Archivo seleccionado: ${file.name} (${Math.round(file.size / 1024)} KB)</small>
                        `;
                        previewImg.style.display = 'block';
                    }

                    // Ocultar video si estaba visible
                    if (video) {
                        video.style.display = 'none';
                    }
                    if (captureBtn) {
                        captureBtn.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }


    // Validación antes de enviar formulario
    const form = document.getElementById('visitanteForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            // Verificar que se haya tomado una foto o subido un archivo (OPCIONAL)
            const fotoBase64 = document.getElementById('fotoBase64');
            const fotoFile = document.querySelector('input[name="foto_file"]');
            const hasBase64 = fotoBase64 && fotoBase64.value && fotoBase64.value.length > 100;
            const hasFile = fotoFile && fotoFile.files.length > 0;

            console.log('=== VALIDACIÓN DE IMAGEN (camara.js) ===');
            console.log('fotoBase64 input existe:', !!fotoBase64);
            console.log('fotoBase64 valor presente:', hasBase64);
            console.log('fotoBase64 longitud:', fotoBase64 ? fotoBase64.value.length : 0);
            console.log('fotoFile input existe:', !!fotoFile);
            console.log('fotoFile archivo presente:', hasFile);
            console.log('fotoFile cantidad archivos:', fotoFile ? fotoFile.files.length : 0);

            if (hasBase64) {
                console.log('✓ Imagen capturada con cámara detectada');
            } else if (hasFile) {
                console.log('✓ Archivo de imagen subido detectado');
            } else {
                console.log('⚠ No se detectó imagen (esto es opcional ahora)');
            }

            // La validación de imagen ahora es opcional - el formulario AJAX lo manejará
            // Solo log para debugging
            console.log('Formulario válido - procede con envío AJAX');
            return true;
        });
    }
});

// Función auxiliar para validar imagen subida
function validateImageFile(input) {
    const file = input.files[0];
    if (!file) return true;

    // Validar tipo de archivo
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        alert('Por favor seleccione un archivo de imagen válido (JPEG, PNG, GIF)');
        input.value = '';
        return false;
    }

    // Validar tamaño (máximo 2MB)
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
        alert('La imagen es muy grande. Por favor seleccione una imagen menor a 2MB.');
        input.value = '';
        return false;
    }

    return true;
}

// Agregar validación a inputs de archivo que se creen dinámicamente
document.addEventListener('change', function (e) {
    if (e.target && e.target.type === 'file' && e.target.accept && e.target.accept.includes('image')) {
        validateImageFile(e.target);
    }
});