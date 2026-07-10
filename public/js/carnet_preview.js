/*
  carnet_preview.js
  - Inicializa Cropper.js dentro del modal #previewModal
  - Permite centrar/zoom de la foto en un recorte 1:1
  - Genera una previsualización compuesta (plantilla + foto circular)
  - Exporta a PDF usando html2canvas + jsPDF
*/

// Cargar fuente Georama
function loadGeormaFont() {
  if (document.fonts && document.fonts.load) {
    // Cargar fuente Georama desde la carpeta font
    const fontFace = new FontFace('Georama', 'url(/font/Georama/Georama-VariableFont_wdth,wght.ttf)');
    fontFace.load().then(function (loadedFont) {
      document.fonts.add(loadedFont);
      console.log('[carnet_preview] Fuente Georama cargada correctamente');
    }).catch(function (error) {
      console.warn('[carnet_preview] Error cargando fuente Georama:', error);
    });
  }
}

// Cargar fuente al inicializar
loadGeormaFont();

// Función para detectar Bootstrap y sus capacidades
function detectBootstrap() {
  const detection = {
    hasBootstrap: false,
    hasJQuery: false,
    version: 'unknown',
    hasGetInstance: false,
    canUseModal: false
  };

  // Detectar jQuery
  if (window.$ && typeof window.$.fn.modal === 'function') {
    detection.hasJQuery = true;
    detection.version = 'jQuery/Bootstrap4';
    detection.canUseModal = true;
  }

  // Detectar Bootstrap 5
  if (window.bootstrap && bootstrap.Modal) {
    detection.hasBootstrap = true;
    detection.version = 'Bootstrap5';
    detection.canUseModal = true;

    if (typeof bootstrap.Modal.getInstance === 'function') {
      detection.hasGetInstance = true;
    }
  }

  console.log('[carnet_preview] Bootstrap detection:', detection);
  return detection;
}

function initCarnetPreview() {
  console.log('[carnet_preview] Iniciando...');

  // Definir colores por cargo
  // Definir colores por cargo (inicialmente vacío)
  let cargos = [];

  // Fetch cargos from API
  fetch('/rrhh/api/cargos')
    .then(response => response.json())
    .then(data => {
      if (data.ok) {
        cargos = data.data.map(c => ({ name: c.descripcion, color: c.color }));
        console.log('[carnet_preview] Cargos loaded:', cargos);
      } else {
        console.error('[carnet_preview] Error loading cargos:', data.error);
      }
    })
    .catch(error => console.error('[carnet_preview] Error fetching cargos:', error));

  // Detectar Bootstrap al inicio
  const bootstrapInfo = detectBootstrap();

  let cropper = null;
  let lastCroppedDataUrl = '';
  let lastComposedDataUrl = '';

  // Función helper para cerrar el modal
  function closeModal(context = '') {
    console.log(`[carnet_preview] Intentando cerrar modal (${context})`);
    let modalClosed = false;

    // Método 1: Bootstrap 5 con getInstance
    if (bootstrapInfo.hasBootstrap && bootstrapInfo.hasGetInstance) {
      try {
        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalInstance.hide();
        modalClosed = true;
        console.log(`[carnet_preview] Modal cerrado con Bootstrap 5 getInstance (${context})`);
      } catch (e) {
        console.warn(`[carnet_preview] Error con Bootstrap 5 getInstance (${context}):`, e);
      }
    }

    // Método 2: Bootstrap 5 sin getInstance
    if (!modalClosed && bootstrapInfo.hasBootstrap) {
      try {
        const modalInstance = new bootstrap.Modal(modalEl);
        modalInstance.hide();
        modalClosed = true;
        console.log(`[carnet_preview] Modal cerrado con Bootstrap 5 nuevo (${context})`);
      } catch (e) {
        console.warn(`[carnet_preview] Error con Bootstrap 5 nuevo (${context}):`, e);
      }
    }

    // Método 3: jQuery/Bootstrap 4
    if (!modalClosed && bootstrapInfo.hasJQuery) {
      try {
        window.$('#previewModal').modal('hide');
        modalClosed = true;
        console.log(`[carnet_preview] Modal cerrado con jQuery (${context})`);
      } catch (e) {
        console.warn(`[carnet_preview] Error con jQuery (${context}):`, e);
      }
    }

    // Método 4: Fallback manual
    if (!modalClosed) {
      console.log(`[carnet_preview] Usando cierre manual (${context})`);
      manualClose();
      modalClosed = true;
    }

    // Verificación adicional después de cualquier método de cierre
    if (modalClosed) {
      setTimeout(verifyAndCleanState, 300);
    }

    return modalClosed;
  }

  const modalEl = document.getElementById('previewModal');
  const modalImg = document.getElementById('modalImage');
  const composeWrap = document.getElementById('composeWrap');
  const cropperContainer = document.getElementById('modalCropperContainer');
  const applyBtn = document.getElementById('applyCropBtn');
  const reEditBtn = document.getElementById('reEditBtn');
  const printBtn = document.getElementById('printPdfBtn');
  const templateBgEl = document.getElementById('templateBg');

  // Función para obtener la ruta de la plantilla dinámicamente
  function getTemplatePath() {
    const backendUrl = window.BACKEND_URL || '';
    if (window.CurrentUser && window.CurrentUser.data && window.CurrentUser.data.institutionLogo) {
      console.log('[carnet_preview] Usando plantilla personalizada de institución:', window.CurrentUser.data.institutionLogo);
      const logo = window.CurrentUser.data.institutionLogo;
      // If already a full URL, use as-is; otherwise prepend backend URL
      if (logo.startsWith('http')) return logo;
      return backendUrl + '/' + logo;
    }
    return backendUrl + '/img/templates/template_id_card.png';
  }

  // Función para normalizar el contenedor
  function normalizeCropperContainer(context = 'unknown') {
    if (!cropperContainer) return;

    console.log(`[carnet_preview] Normalizando contenedor para: ${context}`);

    // Asegurar dimensiones consistentes del contenedor
    cropperContainer.style.width = '100%';
    cropperContainer.style.height = '420px';
    cropperContainer.style.maxWidth = '100%';
    cropperContainer.style.position = 'relative';
    cropperContainer.style.overflow = 'hidden';
    cropperContainer.style.display = 'block';

    // Forzar recálculo
    cropperContainer.offsetHeight;

    console.log(`[carnet_preview] Contenedor normalizado para ${context}:`, {
      clientWidth: cropperContainer.clientWidth,
      clientHeight: cropperContainer.clientHeight,
      offsetWidth: cropperContainer.offsetWidth,
      offsetHeight: cropperContainer.offsetHeight
    });
  }

  // Función de debug integrada - disponible globalmente
  window.debugCarnetDimensions = function (context = 'manual') {
    console.log(`=== DEBUG DIMENSIONES CARNET (${context.toUpperCase()}) ===`);

    // Estado del modal
    console.log('Estado del modal:');
    console.log('- modalEl.classList:', modalEl ? Array.from(modalEl.classList) : 'No encontrado');
    console.log('- modalEl.style.display:', modalEl ? modalEl.style.display : 'No encontrado');
    console.log('- composeWrap.style.display:', composeWrap ? composeWrap.style.display : 'No encontrado');

    if (cropperContainer) {
      const rect = cropperContainer.getBoundingClientRect();
      console.log('cropperContainer:');
      console.log('- clientWidth:', cropperContainer.clientWidth);
      console.log('- clientHeight:', cropperContainer.clientHeight);
      console.log('- getBoundingClientRect:', rect);
      console.log('- style.width:', cropperContainer.style.width);
      console.log('- style.height:', cropperContainer.style.height);

      // Calcular dimensiones como lo hace la función
      const cw = rect.width || cropperContainer.clientWidth || 400;
      const ch = rect.height || cropperContainer.clientHeight || 420;
      const diameter = Math.floor(cw * 0.38);
      const left = Math.floor((cw - diameter) / 2);
      const top = Math.floor(ch * 0.28);

      console.log('Cálculos actuales:');
      console.log('- diameter:', diameter);
      console.log('- left:', left);
      console.log('- top:', top);
    } else {
      console.log('cropperContainer NO encontrado');
    }

    if (modalImg) {
      console.log('modalImg:');
      console.log('- naturalWidth:', modalImg.naturalWidth);
      console.log('- naturalHeight:', modalImg.naturalHeight);
      console.log('- clientWidth:', modalImg.clientWidth);
      console.log('- clientHeight:', modalImg.clientHeight);
      console.log('- src length:', modalImg.src ? modalImg.src.length : 0);
    } else {
      console.log('modalImg NO encontrado');
    }

    // Verificar si hay cropper activo
    if (cropper) {
      console.log('Cropper activo:');
      console.log('- getCropBoxData:', cropper.getCropBoxData());
      console.log('- getContainerData:', cropper.getContainerData());
    } else {
      console.log('No hay cropper activo');
    }

    // Verificar plantilla
    if (templateBgEl) {
      console.log('templateBg:');
      console.log('- display:', window.getComputedStyle(templateBgEl).display);
      console.log('- visibility:', window.getComputedStyle(templateBgEl).visibility);
      console.log('- opacity:', window.getComputedStyle(templateBgEl).opacity);
      console.log('- src:', templateBgEl.src);
    }

    console.log('=== FIN DEBUG DIMENSIONES ===');

    // Retornar datos para uso programático
    return {
      container: cropperContainer ? {
        width: cropperContainer.clientWidth,
        height: cropperContainer.clientHeight,
        rect: cropperContainer.getBoundingClientRect()
      } : null,
      image: modalImg ? {
        naturalWidth: modalImg.naturalWidth,
        naturalHeight: modalImg.naturalHeight,
        clientWidth: modalImg.clientWidth,
        clientHeight: modalImg.clientHeight
      } : null,
      cropper: cropper ? {
        cropBoxData: cropper.getCropBoxData(),
        containerData: cropper.getContainerData()
      } : null
    };
  };

  // Función centralizada para calcular dimensiones del círculo de recorte
  function calculateCropBoxDimensions(context = 'unknown') {
    if (!cropperContainer) return null;

    console.log(`[carnet_preview] calculateCropBoxDimensions llamado desde: ${context}`);

    // Forzar recálculo del layout
    cropperContainer.offsetHeight; // Trigger reflow

    // Esperar un frame para asegurar que el contenedor tenga dimensiones correctas
    const containerRect = cropperContainer.getBoundingClientRect();
    const cw = containerRect.width || cropperContainer.clientWidth || 400; // fallback
    const ch = containerRect.height || cropperContainer.clientHeight || 420; // fallback

    console.log(`[carnet_preview] Dimensiones del contenedor (${context}):`, {
      width: cw,
      height: ch,
      rectWidth: containerRect.width,
      clientWidth: cropperContainer.clientWidth,
      offsetWidth: cropperContainer.offsetWidth
    });

    // Usar dimensiones consistentes
    const diameter = Math.floor(cw * 0.38); // 38% del ancho del contenedor
    const left = Math.floor((cw - diameter) / 2);
    const top = Math.floor(ch * 0.28); // 28% desde arriba

    console.log(`[carnet_preview] Dimensiones del círculo (${context}):`, { diameter, left, top });

    return { diameter, left, top, containerWidth: cw, containerHeight: ch };
  }

  if (!modalEl || !modalImg) {
    console.warn('[carnet_preview] No se encontró el modal o la imagen del modal');
    return;
  }

  // Función de debug para el modal - disponible globalmente
  window.debugCarnetModal = function () {
    console.log('=== DEBUG MODAL CARNET ===');

    if (templateBgEl) {
      console.log('templateBg encontrado:');
      console.log('- display:', window.getComputedStyle(templateBgEl).display);
      console.log('- visibility:', window.getComputedStyle(templateBgEl).visibility);
      console.log('- opacity:', window.getComputedStyle(templateBgEl).opacity);
      console.log('- src:', templateBgEl.src);
      console.log('- naturalWidth:', templateBgEl.naturalWidth);
      console.log('- naturalHeight:', templateBgEl.naturalHeight);
    } else {
      console.log('templateBg NO encontrado');
    }

    if (cropperContainer) {
      console.log('cropperContainer encontrado:');
      console.log('- backgroundImage:', window.getComputedStyle(cropperContainer).backgroundImage);
      console.log('- background:', window.getComputedStyle(cropperContainer).background);
      console.log('- backgroundColor:', window.getComputedStyle(cropperContainer).backgroundColor);
    } else {
      console.log('cropperContainer NO encontrado');
    }

    console.log('Variables internas:');
    console.log('- cropper:', !!cropper);
    console.log('- lastCroppedDataUrl length:', lastCroppedDataUrl.length);
    console.log('- lastComposedDataUrl length:', lastComposedDataUrl.length);

    console.log('=== FIN DEBUG MODAL ===');
  };

  // Exponer una función global para forzar la inicialización desde el botón Visualizar
  window.CarnetPreview_forceInit = function () {
    console.log('[carnet_preview] forceInit llamado');
    try {
      if (!modalImg) {
        console.error('[carnet_preview] modalImg no encontrado');
        return;
      }

      if (!modalImg.src) {
        console.warn('[carnet_preview] modalImg sin src');
        return;
      }

      console.log('[carnet_preview] modalImg.src:', modalImg.src.substring(0, 50) + '...');

      if (typeof Cropper === 'undefined') {
        console.error('[carnet_preview] Cropper no disponible');
        alert('Error: Cropper.js no se cargó correctamente');
        return;
      }

      // Destruir cropper existente de forma segura
      if (cropper) {
        console.log('[carnet_preview] Destruyendo cropper existente');
        try {
          cropper.destroy();
        } catch (e) {
          console.warn('[carnet_preview] Error al destruir cropper:', e);
        }
        cropper = null;
      }

      // Limpiar cualquier elemento cropper residual
      const existingCroppers = document.querySelectorAll('.cropper-container');
      existingCroppers.forEach(container => {
        try {
          container.remove();
        } catch (e) {
          console.warn('[carnet_preview] Error removiendo contenedor cropper:', e);
        }
      });

      // Normalizar contenedor y asegurar que NO haya plantilla de fondo
      if (cropperContainer) {
        normalizeCropperContainer('forceInit');
        cropperContainer.style.backgroundImage = 'none';
        cropperContainer.style.background = 'transparent';
        cropperContainer.style.backgroundColor = 'transparent';
        cropperContainer.style.zIndex = '1';
        cropperContainer.style.display = 'block';
      }

      // Ocultar completamente el elemento de plantilla
      if (templateBgEl) {
        templateBgEl.style.display = 'none';
        templateBgEl.style.visibility = 'hidden';
        templateBgEl.style.opacity = '0';
        templateBgEl.src = 'data:,'; // URL vacía explícita
        templateBgEl.removeAttribute('src'); // Remover atributo completamente
      }

      // Configurar imagen
      modalImg.style.maxWidth = '100%';
      modalImg.style.width = '100%';
      modalImg.style.height = 'auto';
      modalImg.style.pointerEvents = 'auto';
      modalImg.style.display = 'block';
      modalImg.style.userSelect = 'none';
      modalImg.setAttribute('draggable', 'false');
      modalImg.ondragstart = (e) => { e.preventDefault(); return false; };

      // Esperar a que la imagen se cargue
      const initCropper = () => {
        console.log('[carnet_preview] Inicializando cropper...');
        cropper = new Cropper(modalImg, {
          aspectRatio: 1,
          viewMode: 2,
          dragMode: 'crop',
          guides: false,
          background: false,
          autoCropArea: 1,
          movable: true,
          zoomable: true,
          zoomOnWheel: true,
          zoomOnTouch: true,
          wheelZoomRatio: 0.1,
          toggleDragModeOnDblclick: false,
          cropBoxMovable: true,
          cropBoxResizable: false,
          responsive: true,
          scalable: false,
          rotatable: false,
          checkCrossOrigin: false,
          checkOrientation: false,
          ready() {
            console.log('[carnet_preview] Cropper listo (forceInit)');

            // Configurar dimensiones también en forceInit
            setTimeout(() => {
              const dimensions = calculateCropBoxDimensions('forceInit-primera-apertura');
              if (dimensions) {
                cropper.setCropBoxData({
                  left: dimensions.left,
                  top: dimensions.top,
                  width: dimensions.diameter,
                  height: dimensions.diameter
                });
                cropper.setDragMode('move');
                console.log('[carnet_preview] Crop-box configurado (forceInit):', dimensions);

                // Guardar dimensiones de primera apertura si no existen
                if (!firstOpenDimensions) {
                  firstOpenDimensions = { ...dimensions };
                  console.log('[carnet_preview] Dimensiones de primera apertura guardadas:', firstOpenDimensions);
                }

                // Debug automático en desarrollo
                if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                  console.log('[carnet_preview] Debug automático - forceInit primera apertura');
                  window.debugCarnetDimensions('forceInit-primera-apertura');
                }
              }
            }, 100);
          }
        });
      };

      if (modalImg.complete && modalImg.naturalWidth > 0) {
        initCropper();
      } else {
        modalImg.onload = initCropper;
        modalImg.onerror = () => {
          console.error('[carnet_preview] Error cargando imagen');
          alert('Error al cargar la imagen');
        };
      }

    } catch (e) {
      console.error('[carnet_preview] Error en forceInit:', e);
      alert('Error al inicializar el editor de imagen: ' + e.message);
    }
  };
  console.log('[carnet_preview] Inicializado');

  // Variable para almacenar dimensiones de primera apertura
  let firstOpenDimensions = null;

  // Función para comparar dimensiones
  window.compareCarnetDimensions = function () {
    if (!firstOpenDimensions) {
      console.log('⚠️ [carnet_preview] No hay dimensiones de primera apertura para comparar');
      console.log('💡 Esto puede pasar si usaste forceInit() directamente sin abrir el modal normalmente');
      return;
    }

    const current = calculateCropBoxDimensions('comparacion');
    if (!current) return;

    console.log('=== COMPARACIÓN DE DIMENSIONES ===');
    console.log('📏 Primera apertura:', firstOpenDimensions);
    console.log('📏 Actual (re-edición):', current);

    const diameterDiff = Math.abs(firstOpenDimensions.diameter - current.diameter);
    const leftDiff = Math.abs(firstOpenDimensions.left - current.left);
    const topDiff = Math.abs(firstOpenDimensions.top - current.top);
    const containerWidthDiff = Math.abs(firstOpenDimensions.containerWidth - current.containerWidth);
    const containerHeightDiff = Math.abs(firstOpenDimensions.containerHeight - current.containerHeight);

    console.log('🔍 Diferencias:');
    console.log('- diameter:', diameterDiff, diameterDiff === 0 ? '✅ IGUAL' : `❌ DIFERENTE (${diameterDiff}px)`);
    console.log('- left:', leftDiff, leftDiff === 0 ? '✅ IGUAL' : `❌ DIFERENTE (${leftDiff}px)`);
    console.log('- top:', topDiff, topDiff === 0 ? '✅ IGUAL' : `❌ DIFERENTE (${topDiff}px)`);
    console.log('- containerWidth:', containerWidthDiff, containerWidthDiff === 0 ? '✅ IGUAL' : `❌ DIFERENTE (${containerWidthDiff}px)`);
    console.log('- containerHeight:', containerHeightDiff, containerHeightDiff === 0 ? '✅ IGUAL' : `❌ DIFERENTE (${containerHeightDiff}px)`);

    if (diameterDiff === 0 && leftDiff === 0 && topDiff === 0) {
      console.log('🎉 DIMENSIONES DEL CÍRCULO IDÉNTICAS - ¡PROBLEMA RESUELTO!');
    } else {
      console.log('⚠️ DIMENSIONES DEL CÍRCULO DIFERENTES - Revisar por qué');

      if (containerWidthDiff > 0 || containerHeightDiff > 0) {
        console.log('💡 El contenedor tiene dimensiones diferentes, esa puede ser la causa');
      }
    }

    console.log('=== FIN COMPARACIÓN ===');

    return {
      identical: diameterDiff === 0 && leftDiff === 0 && topDiff === 0,
      differences: { diameterDiff, leftDiff, topDiff, containerWidthDiff, containerHeightDiff }
    };
  };

  // Mostrar funciones de debug disponibles
  console.log('[carnet_preview] Funciones de debug disponibles:');
  console.log('- debugCarnetDimensions() - Verificar dimensiones del cropper');
  console.log('- debugCarnetModal() - Verificar estado del modal');
  console.log('- compareCarnetDimensions() - Comparar primera apertura vs actual');
  console.log('- CarnetPreview_forceInit() - Forzar reinicialización');
  // Inyecta estilos para que el área de recorte se vea circular durante el ajuste
  try {
    const styleId = 'carnet-cropper-circle-style';
    if (!document.getElementById(styleId)) {
      const st = document.createElement('style');
      st.id = styleId;
      st.textContent = `
        #previewModal .cropper-view-box,
        #previewModal .cropper-face { border-radius: 50% !important; }
        #previewModal .cropper-view-box { box-shadow: 0 0 0 1px rgba(255,255,255,0.9) inset; }
        #previewModal img#modalImage { cursor: move; touch-action: none; }
        #previewModal .cropper-canvas { cursor: move; }
        #previewModal #modalCropperContainer { position: relative; }
        #previewModal #modalCropperContainer .cropper-container { position: absolute !important; inset: 0; z-index: 2; pointer-events: auto; }
        #previewModal #modalCropperContainer .cropper-drag-box { pointer-events: auto; }
      `;
      document.head.appendChild(st);
    }
  } catch (_) { }

  // Abre/inicia cropper al mostrar modal
  modalEl.addEventListener('shown.bs.modal', function () {
    console.log('[carnet_preview] Modal mostrado');

    // Mostrar las instrucciones cuando se abra el modal
    const instructions = document.getElementById('cropperInstructions');
    if (instructions) { instructions.style.display = 'block'; }

    // Normalizar contenedor y asegurar que NO haya plantilla durante el ajuste
    if (cropperContainer) {
      normalizeCropperContainer('modal-shown');
      cropperContainer.style.display = 'block';
      cropperContainer.style.backgroundImage = 'none';
      cropperContainer.style.background = 'transparent';
      cropperContainer.style.backgroundColor = 'transparent';
      cropperContainer.style.zIndex = '1';
    }
    if (composeWrap) { composeWrap.innerHTML = ''; composeWrap.style.display = 'none'; }
    if (reEditBtn) { reEditBtn.style.display = 'none'; }
    if (templateBgEl) {
      templateBgEl.src = 'data:,'; // URL vacía explícita
      templateBgEl.removeAttribute('src'); // Remover atributo completamente
      templateBgEl.style.display = 'none';
      templateBgEl.style.visibility = 'hidden';
    }
    if (printBtn) { printBtn.disabled = true; }
    const printBackBtn = document.getElementById('printBackBtn');
    if (printBackBtn) { printBackBtn.disabled = true; }
    if (!modalImg || !modalImg.src) return;
    if (printBtn) { printBtn.disabled = true; }
    if (printBackBtn) { printBackBtn.disabled = true; }
    // Espera a que la imagen se cargue para crear el cropper
    const ensureCropper = function () {
      try {
        if (typeof Cropper === 'undefined') {
          alert('No se cargó Cropper.js, revisa la conexión o el CDN.');
          console.error('[carnet_preview] Cropper no definido');
          return;
        }
        if (cropper) { cropper.destroy(); cropper = null; }
        // Asegura que la imagen sea interactuable
        modalImg.style.maxWidth = '100%';
        modalImg.style.width = '100%';
        modalImg.style.height = 'auto';
        modalImg.style.pointerEvents = 'auto';
        modalImg.style.display = 'block';
        modalImg.style.userSelect = 'none';
        modalImg.setAttribute('draggable', 'false');
        modalImg.ondragstart = (e) => { e.preventDefault(); return false; };
        if (cropperContainer) { cropperContainer.style.touchAction = 'none'; cropperContainer.style.pointerEvents = 'auto'; }
        cropper = new Cropper(modalImg, {
          aspectRatio: 1,
          viewMode: 1,
          dragMode: 'move',
          guides: false,
          background: false,
          autoCropArea: 1,
          movable: true,
          zoomable: true,
          zoomOnWheel: true,
          zoomOnTouch: true,
          wheelZoomRatio: 0.1,
          toggleDragModeOnDblclick: false,
          cropBoxMovable: false,
          cropBoxResizable: false,
          responsive: true,
          scalable: false,
          rotatable: false,
          checkCrossOrigin: false,
          checkOrientation: false,
          ready() {
            // Ajustar el crop-box usando función centralizada
            console.log('[carnet_preview] Cropper ready - configurando dimensiones...');
            try {
              // Esperar un poco para que el DOM se estabilice
              setTimeout(() => {
                const dimensions = calculateCropBoxDimensions('primera-apertura');
                if (dimensions) {
                  cropper.setCropBoxData({
                    left: dimensions.left,
                    top: dimensions.top,
                    width: dimensions.diameter,
                    height: dimensions.diameter
                  });
                  cropper.setDragMode('move');
                  console.log('[carnet_preview] Crop-box configurado (primera vez):', dimensions);

                  // Guardar dimensiones de primera apertura
                  firstOpenDimensions = { ...dimensions };

                  // Debug automático en desarrollo
                  if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                    console.log('[carnet_preview] Debug automático - primera apertura');
                    window.debugCarnetDimensions('primera-apertura');
                  }
                }
              }, 100);
            } catch (e) {
              console.warn('[carnet_preview] No se pudo posicionar crop-box:', e);
            }
          }
        });
      } catch (err) {
        console.error('[carnet_preview] Error inicializando Cropper:', err);
      }
    };
    if (modalImg.complete) {
      ensureCropper();
    } else {
      modalImg.onload = ensureCropper;
    }
  });

  // Función de limpieza completa
  function cleanupModal() {
    console.log('[carnet_preview] Limpiando modal...');

    // Destruir cropper de forma segura
    if (cropper) {
      try {
        cropper.destroy();
      } catch (e) {
        console.warn('[carnet_preview] Error al destruir cropper en cleanup:', e);
      }
      cropper = null;
    }

    // Limpiar elementos cropper residuales
    const existingCroppers = document.querySelectorAll('.cropper-container');
    existingCroppers.forEach(container => {
      try {
        container.remove();
      } catch (e) {
        console.warn('[carnet_preview] Error removiendo contenedor en cleanup:', e);
      }
    });

    // Limpiar el contenedor del cropper (sin eliminar el HTML)
    if (cropperContainer) {
      cropperContainer.style.display = 'block';
      cropperContainer.style.backgroundImage = 'none';
      cropperContainer.style.background = 'transparent';
    }

    // Limpiar la imagen del modal (sin eliminarla del DOM)
    if (modalImg) {
      modalImg.src = '';
      modalImg.style.display = 'block';
      modalImg.removeAttribute('style');
      modalImg.style.maxWidth = '100%';
    }

    // Limpiar la imagen de plantilla
    const templateBg = document.getElementById('templateBg');
    if (templateBg) {
      templateBg.src = '';
      templateBg.style.display = 'none';
    }

    // Ocultar el contenedor de composición
    const composeWrap = document.getElementById('composeWrap');
    if (composeWrap) {
      composeWrap.innerHTML = '';
      composeWrap.style.display = 'none';
    }

    // Resetear botones
    if (reEditBtn) { reEditBtn.style.display = 'none'; }
    if (printBtn) { printBtn.disabled = true; }
    if (applyBtn) { applyBtn.disabled = false; }

    // Limpiar variables
    lastCroppedDataUrl = '';
    lastComposedDataUrl = '';

    // Limpiar elementos del modal
    if (composeWrap) {
      composeWrap.innerHTML = '';
      composeWrap.style.display = 'none';
    }

    // Resetear las instrucciones (las mostrará cuando se abra de nuevo)
    const instructions = document.getElementById('cropperInstructions');
    if (instructions) { instructions.style.display = 'block'; }

    if (cropperContainer) {
      cropperContainer.style.display = 'block';
      cropperContainer.style.backgroundImage = 'none';
      cropperContainer.style.background = 'none';
      cropperContainer.removeAttribute('style');
    }

    if (templateBgEl) {
      templateBgEl.src = '';
      templateBgEl.removeAttribute('style');
      templateBgEl.style.display = 'none';
    }

    if (modalImg) {
      modalImg.src = '';
      modalImg.removeAttribute('style');
      modalImg.onload = null;
      modalImg.onerror = null;
    }

    // Resetear botones
    if (reEditBtn) { reEditBtn.style.display = 'none'; }
    if (printBtn) { printBtn.disabled = true; }
    if (applyBtn) { applyBtn.disabled = false; }

    // Limpiar todos los backdrops posibles
    const backdrops = document.querySelectorAll('.modal-backdrop, #modal-backdrop-temp');
    backdrops.forEach(backdrop => {
      try {
        backdrop.remove();
      } catch (e) {
        console.warn('[carnet_preview] Error removiendo backdrop en cleanup:', e);
      }
    });

    // Restaurar completamente el estado del body y documento
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    document.body.style.position = '';
    document.body.style.top = '';

    // Restaurar scroll del documento
    if (document.documentElement.style.overflow === 'hidden') {
      document.documentElement.style.overflow = '';
    }

    // Asegurar que el modal esté completamente oculto
    if (modalEl) {
      modalEl.classList.remove('show');
      modalEl.style.display = 'none';
      modalEl.setAttribute('aria-hidden', 'true');
      modalEl.removeAttribute('aria-modal');
      modalEl.removeAttribute('role');
    }

    console.log('[carnet_preview] Modal y estado de interfaz completamente limpiados');
  }

  // Función para verificar y limpiar estado residual
  function verifyAndCleanState() {
    console.log('[carnet_preview] Verificando estado de la interfaz...');

    // Verificar si hay backdrops residuales
    const backdrops = document.querySelectorAll('.modal-backdrop');
    if (backdrops.length > 0) {
      console.log('[carnet_preview] Encontrados backdrops residuales, limpiando...');
      backdrops.forEach(backdrop => backdrop.remove());
    }

    // Verificar si el body tiene clases residuales
    if (document.body.classList.contains('modal-open')) {
      console.log('[carnet_preview] Body tiene modal-open residual, limpiando...');
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    }

    // Verificar si el modal está visible pero no debería
    if (modalEl && (modalEl.classList.contains('show') || modalEl.style.display === 'block')) {
      if (!modalEl.getAttribute('aria-hidden') || modalEl.getAttribute('aria-hidden') === 'false') {
        console.log('[carnet_preview] Modal visible pero debería estar oculto, corrigiendo...');
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
        modalEl.setAttribute('aria-hidden', 'true');
      }
    }

    console.log('[carnet_preview] Verificación de estado completada');
  }

  // Limpia cropper al cerrar modal (después de que se cierre completamente)
  modalEl.addEventListener('hidden.bs.modal', function () {
    console.log('[carnet_preview] Modal cerrado - ejecutando limpieza');
    setTimeout(() => {
      cleanupModal();
      // Verificación adicional después de la limpieza
      setTimeout(verifyAndCleanState, 200);
    }, 100);
  });

  // También para jQuery Bootstrap (compatibilidad)
  if (window.$ && typeof window.$.fn.modal === 'function') {
    window.$('#previewModal').on('hidden.bs.modal', function () {
      console.log('[carnet_preview] Modal cerrado (jQuery) - ejecutando limpieza');
      setTimeout(cleanupModal, 100);
    });
  }

  // Event listener adicional para botones de cierre específicos
  const closeButtons = modalEl.querySelectorAll('[data-bs-dismiss="modal"], [data-dismiss="modal"], .btn-close, .close');
  closeButtons.forEach(button => {
    button.addEventListener('click', function (e) {
      console.log('[carnet_preview] Click directo en botón de cierre');

      // Forzar cierre si es necesario
      setTimeout(() => {
        if (modalEl.classList.contains('show') || modalEl.style.display === 'block') {
          console.log('[carnet_preview] Modal aún visible, forzando cierre');
          manualClose();
        }
      }, 500);
    });
  });

  // Soporte para tecla Escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modalEl.classList.contains('show')) {
      console.log('[carnet_preview] Tecla Escape presionada');

      closeModal('tecla Escape');
    }
  });

  // Soporte de cierre manual si no hay Bootstrap (fallback)
  const manualClose = function () {
    console.log('[carnet_preview] Cierre manual del modal');

    // Limpiar clases y atributos del modal
    modalEl.classList.remove('show', 'fade');
    modalEl.style.display = 'none';
    modalEl.setAttribute('aria-hidden', 'true');
    modalEl.removeAttribute('aria-modal');
    modalEl.removeAttribute('role');

    // Limpiar todos los backdrops posibles
    const backdrops = document.querySelectorAll('.modal-backdrop, #modal-backdrop-temp');
    backdrops.forEach(backdrop => {
      try {
        backdrop.remove();
      } catch (e) {
        console.warn('[carnet_preview] Error removiendo backdrop:', e);
      }
    });

    // Restaurar completamente el body
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    document.body.style.position = '';
    document.body.style.top = '';

    // Restaurar scroll si estaba bloqueado
    if (document.documentElement.style.overflow === 'hidden') {
      document.documentElement.style.overflow = '';
    }

    // Habilitar todos los elementos que podrían estar deshabilitados
    const disabledElements = document.querySelectorAll('[aria-hidden="true"]:not(#previewModal)');
    disabledElements.forEach(el => {
      if (el !== modalEl) {
        el.removeAttribute('aria-hidden');
      }
    });

    // Restaurar el foco si es necesario
    if (document.activeElement === modalEl || modalEl.contains(document.activeElement)) {
      document.body.focus();
    }

    console.log('[carnet_preview] Estado del modal completamente restaurado');

    // Ejecutar limpieza después de un pequeño delay
    setTimeout(cleanupModal, 150);
  };
  // Manejo de eventos de cierre
  document.addEventListener('click', function (ev) {
    const t = ev.target;
    if (!t) return;

    // Detectar clics en botones de cierre
    if (t.matches('#previewModal [data-dismiss], #previewModal [data-bs-dismiss], #previewModal .btn-close, #previewModal .close')) {
      console.log('[carnet_preview] Click en botón de cierre');
      closeModal('botón de cierre');
    }

    // Detectar clics en el backdrop
    if (t.id === 'previewModal' || t.classList.contains('modal-backdrop')) {
      console.log('[carnet_preview] Click en backdrop');
      closeModal('backdrop');
    }
  });

  // Manejo de tecla Escape
  document.addEventListener('keydown', function (ev) {
    if (ev.key === 'Escape' && modalEl.classList.contains('show')) {
      console.log('[carnet_preview] Tecla Escape presionada');

      if (!(window.bootstrap && bootstrap.Modal) && !(window.$ && typeof window.$.fn.modal === 'function')) {
        ev.preventDefault();
        manualClose();
      }
    }
  });

  // Aplica el recorte y genera previsualización encima de la plantilla (delegado y directo)
  async function onApplyClick() {
    console.log('[carnet_preview] Click Aplicar recorte');
    try {
      if (!modalImg || !modalImg.src) {
        alert('No hay imagen para recortar.');
        return;
      }

      if (!cropper) {
        alert('El editor de imagen no está inicializado. Cierre y vuelva a abrir el modal.');
        return;
      }

      // 1) Canvas cuadrado del recorte (alta resolución para calidad)
      const DPR = Math.max(1, window.devicePixelRatio || 1);
      const baseSize = 1200; // base, luego se multiplica por DPR
      const squareCanvas = cropper.getCroppedCanvas({
        width: Math.floor(baseSize * DPR),
        height: Math.floor(baseSize * DPR),
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
      });
      if (!squareCanvas) return;

      // 2) Convertir a círculo
      const size = Math.min(squareCanvas.width, squareCanvas.height);
      const circleCanvas = document.createElement('canvas');
      circleCanvas.width = size; circleCanvas.height = size;
      const cctx = circleCanvas.getContext('2d');
      cctx.imageSmoothingEnabled = true;
      cctx.imageSmoothingQuality = 'high';
      cctx.save();
      cctx.beginPath();
      cctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
      cctx.closePath();
      cctx.clip();
      cctx.drawImage(squareCanvas, 0, 0, size, size);
      cctx.restore();
      lastCroppedDataUrl = circleCanvas.toDataURL('image/png');

      // 3) Cargar plantilla y componer
      let tplImg;
      try {
        const currentTemplatePath = getTemplatePath();
        const res = await fetch('/proxy-image?url=' + encodeURIComponent(currentTemplatePath));
        if (!res.ok) throw new Error('Network response was not ok');
        const blob = await res.blob();
        const objUrl = URL.createObjectURL(blob);
        tplImg = await loadImage(objUrl);
      } catch (e) {
        console.error('[carnet_preview] No se pudo cargar la plantilla:', getTemplatePath(), e);
        alert('No se pudo cargar la plantilla del carnet.');
        return;
      }

      const outW = tplImg.naturalWidth;
      const outH = tplImg.naturalHeight;
      const outCanvas = document.createElement('canvas');
      outCanvas.width = outW; outCanvas.height = outH;
      const octx = outCanvas.getContext('2d');
      octx.imageSmoothingEnabled = true;
      octx.imageSmoothingQuality = 'high';
      octx.fillStyle = '#ffffff';
      octx.fillRect(0, 0, outW, outH);
      octx.drawImage(tplImg, 0, 0, outW, outH);

      const circleImg = await loadImage(lastCroppedDataUrl);
      const circleDiameter = Math.floor(outW * 0.55); // Agrandado de 0.38 a 0.42
      const cx = Math.floor((outW - circleDiameter) / 2);
      const cy = Math.floor(outH * 0.25); // Subido de 0.28 a 0.25 para dar más espacio abajo

      // Clip circular para asegurar borde perfecto
      octx.save();
      octx.beginPath();
      octx.arc(cx + circleDiameter / 2, cy + circleDiameter / 2, circleDiameter / 2, 0, Math.PI * 2);
      octx.closePath();
      octx.clip();
      octx.drawImage(circleImg, cx, cy, circleDiameter, circleDiameter);
      octx.restore();

      // 4) Texto: Nombre Apellido, Cédula, Grupo Sanguíneo, Departamento, Cargo
      try {
        const u = (window.CurrentUser && window.CurrentUser.data) ? window.CurrentUser.data : null;

        // Obtener datos del usuario y convertir a mayúsculas
        const nombreCompleto = u ? (u.primerNombre + ' ' + u.primerApellido).trim().toUpperCase() : 'NOMBRE APELLIDO';
        const cedulaTxt = u ? (u.cedulaCompleta || u.cedula || '') : 'Cédula';
        const grupo = u ? ('Grupo Sanguíneo: ' + (u.grupoSanguineo || 'O+')) : 'Grupo Sanguíneo: O+';

        // Obtener departamento y cargo (estos vendrán de los helpers)
        const departamento = u ? (u.departamentoNombre || 'DEPARTAMENTO').toUpperCase() : 'DEPARTAMENTO';
        const cargo = u ? (u.cargoNombre || 'CARGO').toUpperCase() : 'CARGO';

        octx.fillStyle = '#111';
        octx.textAlign = 'center';
        octx.textBaseline = 'top';

        // Cargar fuente Georama si está disponible
        const fontFamily = 'Georama, Arial, sans-serif';

        // Función para calcular y dibujar texto con medidas exactas
        function drawTextWithFallback(text, x, y, maxWidth, baseFontSize, fontWeight = '', maxLines = 2) {
          // Cálculos basados en medidas finales exactas del carnet
          // Carnet final: 85mm x 55mm (850px x 550px a 300 DPI)


          const carnetWidthMM = 85;
          const carnetHeightMM = 55;
          const pixelsPerMM = outW / carnetWidthMM; // Relación píxeles por mm

          // Calcular tamaño de fuente óptimo basado en el espacio disponible
          const availableWidthMM = (maxWidth / pixelsPerMM);
          const availableHeightMM = (baseFontSize * maxLines * 1.2) / pixelsPerMM;

          // Función para calcular el tamaño de fuente óptimo
          function calculateOptimalFontSize(text, maxWidthPx, maxLines) {
            let testFontSize = baseFontSize;
            let bestFontSize = baseFontSize;
            let bestLines = [];

            // Probar diferentes tamaños de fuente desde el máximo hacia abajo
            for (let fontSize = baseFontSize; fontSize >= baseFontSize * 0.5; fontSize -= 1) {
              octx.font = `${fontWeight} ${fontSize}px ${fontFamily}`;

              const lines = wrapTextPrecise(text, maxWidthPx);

              if (lines.length <= maxLines) {
                bestFontSize = fontSize;
                bestLines = lines;
                break;
              }
            }

            return { fontSize: bestFontSize, lines: bestLines };
          }

          // Función para dividir texto con medidas precisas
          function wrapTextPrecise(text, maxWidthPx) {
            const words = text.split(' ');
            const lines = [];
            let currentLine = '';

            for (const word of words) {
              const testLine = currentLine + (currentLine ? ' ' : '') + word;
              const testWidth = octx.measureText(testLine).width;

              if (testWidth > maxWidthPx && currentLine) {
                lines.push(currentLine.trim());
                currentLine = word;
              } else {
                currentLine = testLine;
              }
            }

            if (currentLine.trim()) {
              lines.push(currentLine.trim());
            }

            return lines;
          }

          // Calcular el resultado óptimo
          const result = calculateOptimalFontSize(text, maxWidth, maxLines);
          let { fontSize, lines } = result;

          // Si aún hay demasiadas líneas, truncar la última
          if (lines.length > maxLines) {
            lines = lines.slice(0, maxLines);
            let lastLine = lines[maxLines - 1];

            // Asegurar que la última línea con "..." quepa
            octx.font = `${fontWeight} ${fontSize}px ${fontFamily}`;
            const ellipsis = '...';

            while (octx.measureText(lastLine + ellipsis).width > maxWidth && lastLine.length > 0) {
              // Remover palabra por palabra, no carácter por carácter
              const words = lastLine.split(' ');
              if (words.length > 1) {
                words.pop();
                lastLine = words.join(' ');
              } else {
                // Si solo queda una palabra, truncar por caracteres
                lastLine = lastLine.slice(0, -1);
              }
            }

            lines[maxLines - 1] = lastLine + ellipsis;
          }

          // Configurar fuente final
          octx.font = `${fontWeight} ${fontSize}px ${fontFamily}`;

          // Calcular posición vertical centrada
          const lineHeight = fontSize * 1.1; // Espaciado más compacto
          const totalHeight = lines.length * lineHeight;
          const startY = y - (totalHeight / 2) + (fontSize * 0.3); // Ajuste fino de centrado

          // Dibujar las líneas
          lines.forEach((line, index) => {
            const lineY = startY + (index * lineHeight);
            octx.fillText(line, x, lineY);
          });

          return fontSize;
        }

        // Nombre (negrita grande) - calculado para medidas exactas
        octx.fillStyle = '#111';
        // Tamaño base calculado para carnet de 85mm: ~3.5mm de altura = ~42px a 300 DPI
        const nombreFontSize = Math.floor(outW * 0.05); // Reducido para mejor ajuste
        const maxNombreWidth = outW * 0.85; // 85% del ancho para mejor margen
        drawTextWithFallback(nombreCompleto, outW / 2, cy + circleDiameter + Math.floor(outH * 0.02), maxNombreWidth, nombreFontSize, 'bold', 2);

        // Cédula (más grande, gris, con bold sutil) - más cerca del nombre
        octx.fillStyle = '#222';
        octx.font = `500 ${Math.floor(outW * 0.05)}px ${fontFamily}`; // 500 = medium weight
        octx.fillText(cedulaTxt, outW / 2, cy + circleDiameter + Math.floor(outH * 0.06)); // Bajado más cerca

        // Grupo sanguíneo (más grande, con bold sutil) - más cerca de la cédula
        octx.fillStyle = '#222';
        octx.font = `500 ${Math.floor(outW * 0.05)}px ${fontFamily}`; // 500 = medium weight
        octx.fillText(grupo, outW / 2, cy + circleDiameter + Math.floor(outH * 0.10)); // Bajado más cerca

        // Departamento (DEBAJO del logo, en mayúsculas) - calculado para medidas exactas
        octx.fillStyle = '#333';
        // Tamaño base calculado para carnet de 85mm: ~2.5mm de altura = ~30px a 300 DPI
        const deptFontSize = Math.floor(outW * 0.040); // Reducido para mejor ajuste
        const maxDeptWidth = outW * 0.80; // 80% del ancho para mejor margen
        // Posición debajo del logo
        drawTextWithFallback(departamento, outW / 2, cy + circleDiameter + Math.floor(outH * 0.25), maxDeptWidth, deptFontSize, '', 2);

        // Buscar el color del cargo
        const cargoColor = cargos.find(c => c.name.toUpperCase() === cargo.toUpperCase())?.color || '1D70B7'; // Color azul por defecto

        // Dibujar la franja de color para el cargo
        const franjaHeight = Math.floor(outH * 0.12); // Altura de la franja (12% del alto)
        const franjaY = outH - franjaHeight; // Posición desde abajo

        octx.fillStyle = `#${cargoColor}`;
        octx.fillRect(0, franjaY, outW, franjaHeight);

        // Cargo en la franja de color (negrita) - calculado para medidas exactas
        octx.fillStyle = '#FFFFFF';
        // Tamaño base calculado para franja de ~8mm de altura = ~32px a 300 DPI
        const cargoFontSize = Math.floor(outW * 0.050); // Ajustado para la franja
        const maxCargoWidth = outW * 0.90; // 90% del ancho, no más del carnet
        // Posición centrada en la franja de color
        drawTextWithFallback(cargo, outW / 2, outH - Math.floor(outH * 0.07), maxCargoWidth, cargoFontSize, 'bold', 2);

      } catch (e) {
        console.error('Error al renderizar texto del carnet:', e);
      }
      // end texto

      // Para la previsualización, usar la imagen original sin rotar
      lastComposedDataUrl = outCanvas.toDataURL('image/png');

      // 4) Ocultar el cropper y mostrar solo la previsualización centrada
      if (cropper) { cropper.destroy(); cropper = null; }
      if (cropperContainer) { cropperContainer.style.display = 'none'; }

      // Ocultar las instrucciones cuando se muestre la previsualización
      const instructions = document.getElementById('cropperInstructions');
      if (instructions) { instructions.style.display = 'none'; }

      if (composeWrap) {
        composeWrap.innerHTML = '';
        const img = document.createElement('img');
        img.id = 'composedPreview';
        img.src = lastComposedDataUrl;
        img.alt = 'Previsualización del carnet';
        img.style.maxWidth = '100%';
        img.style.height = 'auto';
        img.style.display = 'block';
        img.style.margin = '0 auto';
        composeWrap.appendChild(img);
        composeWrap.style.display = 'block';
      }
      if (printBtn) { printBtn.disabled = false; }
      const printBackBtn = document.getElementById('printBackBtn');
      if (printBackBtn) { printBackBtn.disabled = false; }
      if (reEditBtn) { reEditBtn.style.display = 'inline-block'; }

    } catch (e) {
      console.error('[carnet_preview] Error al aplicar recorte:', e);
      alert('Ocurrió un error al aplicar el recorte.');
    }
  }

  async function onPrintClick() {
    console.group('[DEBUG] onPrintClick - Solo Frontal');
    console.time('[DEBUG] Tiempo total onPrintClick');

    // Verificar que se haya seleccionado un carnet O que el empleado tenga carnet asociado
    if (window.selectedCarnetForPrint) {
      console.log('[DEBUG] Carnet seleccionado para impresión:', window.selectedCarnetForPrint);
    } else {
      // Verificar si hay carnet asociado al empleado
      const hasAssociatedCarnet = await checkEmployeeCarnet();
      if (!hasAssociatedCarnet) {
        console.warn('[DEBUG] No hay carnet seleccionado ni asociado');
        alert('Este empleado no tiene un carnet asignado. Debe asignar un carnet desde la gestión de carnets antes de imprimir.');
        console.groupEnd();
        return;
      }
    }

    if (!lastComposedDataUrl) {
      console.warn('[DEBUG] lastComposedDataUrl vacío, no se ha aplicado recorte');
      alert('Primero aplica el recorte para generar la previsualización.');
      console.groupEnd();
      return;
    }
    console.log('[DEBUG] Longitud lastComposedDataUrl:', lastComposedDataUrl.length);
    try {
      if (typeof jsPDF === 'undefined') {
        console.log('[DEBUG] jsPDF no cargado, cargando librería...');
        await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
        console.log('[DEBUG] jsPDF cargado');
      }
      const pageWidth = 55; // mm
      const pageHeight = 85; // mm
      console.log('[DEBUG] Formato páginas (mm):', { pageWidth, pageHeight });
      async function composeToPortraitPageData(src) {
        console.log('[DEBUG] composeToPortraitPageData inicio src length:', src.length);
        const imgEl = await loadImage(src);
        console.log('[DEBUG] Imagen cargada para compose (front only):', { w: imgEl.width, h: imgEl.height });
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(pageWidth * 10);
        canvas.height = Math.round(pageHeight * 10);
        const ctx = canvas.getContext('2d');
        const scaleX = canvas.width / imgEl.width;
        const scaleY = canvas.height / imgEl.height;
        const scale = Math.max(scaleX, scaleY) * 1.01;
        ctx.save();
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.drawImage(imgEl, -imgEl.width * scale / 2, -imgEl.height * scale / 2, imgEl.width * scale, imgEl.height * scale);
        ctx.restore();
        const out = canvas.toDataURL('image/png');
        console.log('[DEBUG] composeToPortraitPageData fin output length:', out.length);
        return out;
      }
      console.log('[DEBUG] Generando SOLO página frontal...');
      const frontPageData = await composeToPortraitPageData(lastComposedDataUrl);
      console.log('[DEBUG] Página frontal generada length:', frontPageData.length);

      // Crear PDF solo con la parte frontal
      // @ts-ignore
      const pdf = new jspdf.jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: [pageWidth, pageHeight],
        compress: true,
        precision: 16,
        putOnlyUsedFonts: true,
        floatPrecision: 16
      });
      console.log('[DEBUG] PDF creado para solo frontal');

      pdf.setDisplayMode('fullwidth', 'continuous');
      const oversize = 0.1;
      pdf.addImage(frontPageData, 'PNG', -oversize, -oversize, pageWidth + (oversize * 2), pageHeight + (oversize * 2), undefined, 'FAST');
      console.log('[DEBUG] Imagen frontal añadida al PDF');

      // Agregar información del carnet seleccionado como metadatos
      if (window.selectedCarnetForPrint) {
        pdf.setProperties({
          title: `Carnet Frontal - ${window.selectedCarnetForPrint.codigo}`,
          subject: 'Carnet de Empleado - Parte Frontal',
          creator: 'Sistema de Carnetización RRHH'
        });
      }

      pdf.autoPrint();
      const pdfBlob = pdf.output('blob');
      const pdfUrl = URL.createObjectURL(pdfBlob);
      console.log('[DEBUG] PDF Blob URL generado:', pdfUrl);
      const printWindow = window.open(pdfUrl, '_blank');
      if (printWindow) {
        printWindow.onload = function () {
          console.log('[DEBUG] Ventana de impresión cargada, enviando print()');
          printWindow.print();
          setTimeout(() => { URL.revokeObjectURL(pdfUrl); console.log('[DEBUG] Blob URL revocado'); }, 1000);
        };
      } else {
        console.warn('[DEBUG] No se pudo abrir ventana para imprimir (popup bloqueado)');
        alert('Permite ventanas emergentes para imprimir el carnet.');
      }
    } catch (error) {
      console.error('[DEBUG] Error en onPrintClick:', error);
      alert('Error al generar el PDF. Revisa la consola para detalles.');
    } finally {
      console.timeEnd('[DEBUG] Tiempo total onPrintClick');
      console.groupEnd();
    }
  }
  // Función auxiliar para cargar scripts dinámicamente
  function loadScript(src) {
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = src;
      script.onload = resolve;
      script.onerror = () => reject(new Error(`Error al cargar el script: ${src}`));
      document.head.appendChild(script);
    });
  }

  // Función para generar la parte trasera del carnet con QR
  async function onPrintBackClick() {
    try {
      console.log('[DEBUG] onPrintBackClick - Asignar Trasera');

      // Verificar que se haya seleccionado un carnet O que el empleado tenga carnet asociado
      if (window.selectedCarnetForPrint) {
        console.log('[DEBUG] Carnet seleccionado para trasera:', window.selectedCarnetForPrint);
      } else {
        // Verificar si hay carnet asociado al empleado
        const hasAssociatedCarnet = await checkEmployeeCarnet();
        if (!hasAssociatedCarnet) {
          console.warn('[DEBUG] No hay carnet seleccionado ni asociado para trasera');
          alert('Este empleado no tiene un carnet asignado. Debe asignar un carnet desde la gestión de carnets antes de imprimir la parte trasera.');
          return;
        }
      }

      // Verificar que hay datos del usuario
      const u = (window.CurrentUser && window.CurrentUser.data) ? window.CurrentUser.data : null;
      if (!u || !u.cedula) {
        alert('No hay información del usuario para generar el código QR.');
        return;
      }

      // Cargar jsPDF dinámicamente si no está disponible
      if (typeof jsPDF === 'undefined') {
        await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
      }

      // Generar código QR
      const qrData = await generateQRWithFallback(u.cedula);

      if (!qrData.success) {
        throw new Error(qrData.error || 'Error generando código QR');
      }

      // Cargar la plantilla de la parte trasera
      const TEMPLATE_BACK_PATH = '/img/templates/template_id_card_out.png';
      let templateBackImg;
      try {
        templateBackImg = await loadImage(TEMPLATE_BACK_PATH);
      } catch (e) {
        console.error('[carnet_preview] No se pudo cargar la plantilla trasera:', TEMPLATE_BACK_PATH, e);
        alert('No se pudo cargar la plantilla de la parte trasera del carnet.');
        return;
      }

      // Crear canvas para la parte trasera usando las dimensiones de la plantilla
      const backCanvas = document.createElement('canvas');
      backCanvas.width = templateBackImg.naturalWidth;
      backCanvas.height = templateBackImg.naturalHeight;
      const backCtx = backCanvas.getContext('2d');
      backCtx.imageSmoothingEnabled = true;
      backCtx.imageSmoothingQuality = 'high';

      // Dibujar la plantilla de fondo
      backCtx.drawImage(templateBackImg, 0, 0, backCanvas.width, backCanvas.height);

      // Cargar imagen del QR
      const qrImg = new Image();
      qrImg.crossOrigin = 'anonymous';

      await new Promise((resolve, reject) => {
        qrImg.onload = resolve;
        qrImg.onerror = reject;
        qrImg.src = qrData.qr_base64;
      });

      // Dibujar QR pequeño en la esquina inferior derecha
      const qrSize = Math.floor(backCanvas.width * 0.30); // 15% del ancho del carnet (pequeño)
      const qrX = backCanvas.width - qrSize - Math.floor(backCanvas.width * 0.05); // 5% de margen desde la derecha
      const qrY = backCanvas.height - qrSize - Math.floor(backCanvas.height * 0.04); // 8% de margen desde abajo


      // Dibujar el QR
      backCtx.drawImage(qrImg, qrX, qrY, qrSize, qrSize);

      // Rotar para orientación horizontal (igual que el frente)
      const pdfCanvas = document.createElement('canvas');

      // Usar las proporciones exactas del PDF (85mm x 55mm = 1.545:1)
      const pdfRatio = 85 / 55; // 1.545
      pdfCanvas.width = Math.round(850);   // 8.5 cm horizontal
      pdfCanvas.height = Math.round(850 / pdfRatio); // Altura proporcional exacta

      const pdfCtx = pdfCanvas.getContext('2d');
      pdfCtx.imageSmoothingEnabled = true;
      pdfCtx.imageSmoothingQuality = 'high';

      // Limpiar completamente el canvas
      pdfCtx.clearRect(0, 0, pdfCanvas.width, pdfCanvas.height);

      // Rotar la parte trasera
      pdfCtx.save();
      pdfCtx.translate(pdfCanvas.width / 2, pdfCanvas.height / 2);
      pdfCtx.rotate(Math.PI / 2);

      // Calcular escala para llenar completamente el canvas
      const scaleX = pdfCanvas.width / backCanvas.height;  // Intercambiado por rotación
      const scaleY = pdfCanvas.height / backCanvas.width;   // Intercambiado por rotación
      const scale = Math.max(scaleX, scaleY) * 1.01; // Ligeramente mayor para eliminar bordes

      // Dibujar la parte trasera rotada y escalada
      pdfCtx.drawImage(
        backCanvas,
        -backCanvas.width * scale / 2,
        -backCanvas.height * scale / 2,
        backCanvas.width * scale,
        backCanvas.height * scale
      );

      pdfCtx.restore();

      // Obtener imagen rotada
      const rotatedBackData = pdfCanvas.toDataURL('image/png');

      // Crear PDF
      const pageWidth = 85;    // 8.5 cm = 85 mm
      const pageHeight = 55;   // 5.5 cm = 55 mm

      // @ts-ignore - jsPDF está disponible globalmente
      const pdf = new jspdf.jsPDF({
        orientation: 'landscape',
        unit: 'mm',
        format: [pageWidth, pageHeight],
        compress: true,
        precision: 16,
        putOnlyUsedFonts: true,
        floatPrecision: 16
      });

      // Configurar para eliminar cualquier margen interno del PDF
      pdf.setDisplayMode('fullwidth', 'continuous');

      // Agregar imagen con ligero sobredimensionado para eliminar bordes
      const oversize = 0.1; // 0.1mm de sobredimensionado
      pdf.addImage(
        rotatedBackData,
        'PNG',
        -oversize,                    // x ligeramente negativo
        -oversize,                    // y ligeramente negativo
        pageWidth + (oversize * 2),   // ancho ligeramente mayor
        pageHeight + (oversize * 2),  // alto ligeramente mayor
        undefined,
        'FAST'
      );

      // Imprimir directamente
      pdf.autoPrint();

      const pdfBlob = pdf.output('blob');
      const pdfUrl = URL.createObjectURL(pdfBlob);
      console.log('[DEBUG] PDF Blob URL generado:', pdfUrl);
      const printWindow = window.open(pdfUrl, '_blank');
      if (printWindow) {
        printWindow.onload = function () {
          console.log('[DEBUG] Ventana de impresión cargada, enviando print()');
          printWindow.print();
          setTimeout(() => { URL.revokeObjectURL(pdfUrl); console.log('[DEBUG] Blob URL revocado'); }, 1000);
        };
      } else {
        console.warn('[DEBUG] No se pudo abrir ventana para imprimir (popup bloqueado)');
        alert('Permite ventanas emergentes para imprimir el carnet.');
      }

    } catch (error) {
      console.error('Error al generar la parte trasera:', error);
      alert('Error al generar la parte trasera del carnet: ' + error.message);
    }
  }

  if (applyBtn) { applyBtn.addEventListener('click', onApplyClick); }
  if (printBtn) { printBtn.addEventListener('click', onPrintClick); }

  // Event listener para la parte trasera
  const printBackBtn = document.getElementById('printBackBtn');
  if (printBackBtn) { printBackBtn.addEventListener('click', onPrintBackClick); }
  if (reEditBtn) {
    reEditBtn.addEventListener('click', function () {
      try {
        // Volver a mostrar el cropper y ocultar la previsualización compuesta
        if (composeWrap) { composeWrap.innerHTML = ''; composeWrap.style.display = 'none'; }

        // Mostrar las instrucciones cuando se vuelva a editar
        const instructions = document.getElementById('cropperInstructions');
        if (instructions) { instructions.style.display = 'block'; }

        if (cropperContainer) {
          normalizeCropperContainer('re-edicion');
          cropperContainer.style.display = 'block';
          // NO mostrar plantilla durante re-edición
          cropperContainer.style.backgroundImage = 'none';
          cropperContainer.style.background = 'transparent';
          cropperContainer.style.backgroundColor = 'transparent';
        }

        // Ocultar completamente el elemento de plantilla durante re-edición
        if (templateBgEl) {
          templateBgEl.style.display = 'none';
          templateBgEl.style.visibility = 'hidden';
          templateBgEl.style.opacity = '0';
          templateBgEl.src = 'data:,'; // URL vacía explícita
          templateBgEl.removeAttribute('src'); // Remover atributo completamente
        }
        if (!modalImg || !modalImg.src) { alert('No hay imagen para re-editar.'); return; }
        if (cropper) { cropper.destroy(); cropper = null; }
        // Re-crear el cropper con la misma configuración
        modalImg.style.maxWidth = '100%';
        modalImg.style.width = '100%';
        modalImg.style.height = 'auto';
        modalImg.style.pointerEvents = 'auto';
        modalImg.style.display = 'block';
        modalImg.style.userSelect = 'none';
        modalImg.setAttribute('draggable', 'false');
        modalImg.ondragstart = (e) => { e.preventDefault(); return false; };
        if (cropperContainer) { cropperContainer.style.touchAction = 'none'; cropperContainer.style.pointerEvents = 'auto'; }
        cropper = new Cropper(modalImg, {
          aspectRatio: 1,
          viewMode: 2,
          dragMode: 'move',
          guides: false,
          background: false,
          autoCropArea: 1,
          movable: true,
          zoomable: true,
          zoomOnWheel: true,
          zoomOnTouch: true,
          wheelZoomRatio: 0.1,
          toggleDragModeOnDblclick: false,
          cropBoxMovable: false,
          cropBoxResizable: false,
          responsive: true,
          scalable: false,
          rotatable: false,
          ready() {
            // Usar la misma función centralizada para re-edición
            console.log('[carnet_preview] Re-edición ready - configurando dimensiones...');
            try {
              setTimeout(() => {
                const dimensions = calculateCropBoxDimensions('re-edicion');
                if (dimensions) {
                  cropper.setCropBoxData({
                    left: dimensions.left,
                    top: dimensions.top,
                    width: dimensions.diameter,
                    height: dimensions.diameter
                  });
                  cropper.setDragMode('move');
                  console.log('[carnet_preview] Re-edición crop-box configurado:', dimensions);

                  // Debug automático en desarrollo para comparar
                  if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                    console.log('[carnet_preview] Debug automático - re-edición');
                    window.debugCarnetDimensions('re-edicion');

                    // Comparación automática
                    setTimeout(() => {
                      window.compareCarnetDimensions();
                    }, 100);
                  }
                }
              }, 200); // Más tiempo para re-edición
            } catch (e) {
              console.warn('[carnet_preview] Error en re-edición crop-box:', e);
            }
          }
        });
        if (printBtn) { printBtn.disabled = true; }
        if (reEditBtn) { reEditBtn.style.display = 'none'; }
      } catch (e) {
        console.error('[carnet_preview] Error al re-editar:', e);
      }
    });
  }
  document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'applyCropBtn') { onApplyClick.call(e.target); }
    if (e.target && e.target.id === 'printPdfBtn') { onPrintClick.call(e.target); }
    if (e.target && e.target.id === 'reEditBtn') { e.preventDefault(); /* handled by listener */ }
  });

  // Utilidad: cargar imagen como promesa
  function loadImage(src) {
    return new Promise(function (resolve, reject) {
      const im = new Image();
      im.onload = () => resolve(im);
      im.onerror = reject;
      // Permite cargar recursos locales relativos
      im.crossOrigin = 'anonymous';
      im.src = src;
    });
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCarnetPreview);
} else {
  // DOM ya listo
  initCarnetPreview();
}

// Función para verificar si el empleado tiene carnet asociado
async function checkEmployeeCarnet() {
  try {
    const user = window.CurrentUser?.data;
    if (!user || !user.id) {
      console.warn('[DEBUG] No hay datos de usuario para verificar carnet');
      return false;
    }

    const response = await fetch(`/rrhh/api/empleado/${user.id}/carnet`);
    const data = await response.json();

    console.log('[DEBUG] Verificación carnet empleado:', data);
    return data.ok;
  } catch (error) {
    console.error('[DEBUG] Error verificando carnet del empleado:', error);
    return false;
  }
}

// Función helper para generar QR con fallback
async function generateQRWithFallback(cedula) {
  console.log('[DEBUG] Generando QR para cédula:', cedula);

  // Obtener la URL base del sitio
  const baseUrl = window.location.origin;
  console.log('[DEBUG] Base URL:', baseUrl);

  // Intentar primero con el archivo PHP original
  try {
    const phpUrl = `${baseUrl}/helper/generator-qr.php`;
    console.log('[DEBUG] Intentando PHP URL:', phpUrl);

    const qrResponse = await fetch(phpUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `user_id=${encodeURIComponent(cedula)}`
    });

    console.log('[DEBUG] Estado respuesta QR (PHP):', qrResponse.status);
    console.log('[DEBUG] Headers respuesta:', qrResponse.headers);

    if (qrResponse.ok) {
      const responseText = await qrResponse.text();
      console.log('[DEBUG] Respuesta PHP raw:', responseText.substring(0, 200));

      try {
        const qrData = JSON.parse(responseText);
        console.log('[DEBUG] QR generado exitosamente con PHP');
        return qrData;
      } catch (parseError) {
        console.error('[DEBUG] Error parsing JSON from PHP:', parseError);
        throw new Error('Invalid JSON response from PHP generator');
      }
    } else {
      const errorText = await qrResponse.text();
      console.error('[DEBUG] Error response from PHP:', errorText);
      throw new Error(`PHP generator failed with status: ${qrResponse.status}`);
    }
  } catch (error) {
    console.log('[DEBUG] Fallback a Laravel QR generator:', error.message);

    // Fallback a la ruta de Laravel
    try {
      const laravelUrl = `${baseUrl}/api/generate-qr`;
      console.log('[DEBUG] Intentando Laravel URL:', laravelUrl);

      const qrResponse = await fetch(laravelUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: `user_id=${encodeURIComponent(cedula)}`
      });

      console.log('[DEBUG] Estado respuesta QR (Laravel):', qrResponse.status);

      if (qrResponse.ok) {
        const responseText = await qrResponse.text();
        console.log('[DEBUG] Respuesta Laravel raw:', responseText.substring(0, 200));

        try {
          const qrData = JSON.parse(responseText);
          console.log('[DEBUG] QR generado exitosamente con Laravel');
          return qrData;
        } catch (parseError) {
          console.error('[DEBUG] Error parsing JSON from Laravel:', parseError);
          throw new Error('Invalid JSON response from Laravel generator');
        }
      } else {
        const errorText = await qrResponse.text();
        console.error('[DEBUG] Error response from Laravel:', errorText);
        throw new Error(`Laravel generator failed with status: ${qrResponse.status}`);
      }
    } catch (laravelError) {
      console.error('[DEBUG] Ambos generadores QR fallaron:', laravelError);
      throw new Error('No se pudo generar el código QR con ningún método disponible');
    }
  }
}