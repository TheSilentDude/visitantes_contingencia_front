@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">
            <i class="fas fa-qrcode me-2"></i>Gestión de Carnets de Visitantes
        </h1>
        <div>
            @if(session('user_rol_id') == 6)
            <a href="{{ route('impresiones_reversa.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
            </a>
            @endif
        </div>
    </div>

    <!-- Panel de Generación de Carnets -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-plus-circle me-2"></i>Generar Nuevos Carnets
            </h6>
        </div>
        <div class="card-body">
            <form id="generarForm">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-4" style="display: none;">
                        <input type="hidden" id="piso_seleccionado" name="piso_seleccionado" value="N/A">
                    </div>

                    <div class="col-md-4">
                        <label for="cantidad_carnets" class="form-label">Cantidad de carnets</label>
                        <select id="cantidad_carnets" name="cantidad_carnets" class="form-control" required>
                            <option value="" disabled>-- Cantidad --</option>
                            @for($cantidad = 1; $cantidad <= 5; $cantidad++) <option value="{{ $cantidad }}">{{
                                $cantidad }} carnet{{ $cantidad > 1 ? 'es' : '' }}</option>
                                @endfor
                        </select>
                    </div>

                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="fas fa-cogs me-2"></i>Generar Parte Trasera
                            </button>
                            <button class="btn btn-success btn-lg" type="button" id="generar_delante">
                                <i class="fas fa-id-card me-2"></i>Generar Parte Delantera
                            </button>
                        </div>
                    </div>
                </div>
                <small class="form-text text-muted mt-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Seleccione el piso y la cantidad (máx. 5) de carnets para generar los códigos QR de los visitantes
                </small>
            </form>
        </div>
    </div>

    <!-- Historial de Carnets -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>Historial de Carnets Generados
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableHistorial" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Archivo</th>
                            <th>Cantidad</th>
                            <th>Generado por</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historialCarnets as $registro)
                        <tr>
                            <td class="align-middle" data-order="{{ (int) ($registro->id ?? 0) }}">
                                <span class="text-dark font-weight-bold"
                                    style="font-size: 1.2rem; display: block; padding: 2px 5px; border-left: 4px solid #4e73df; background: #f8f9fc;">#{{
                                    $registro->id }}</span>
                            </td>
                            <td class="align-middle">{{ basename($registro->ruta_archivo ?? 'N/A') }}</td>
                            <td>{{ $registro->cantidad_carnets ?? 1 }}</td>

                            <td>
                                @if($registro->generatedBy && $registro->generatedBy->empleado)
                                {{ $registro->generatedBy->empleado->primer_nombre }} {{
                                $registro->generatedBy->empleado->primer_apellido }}
                                @else
                                N/A
                                @endif
                            </td>
                            <td data-order="{{ \Carbon\Carbon::parse($registro->created_at)->timestamp }}">{{ \Carbon\Carbon::parse($registro->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($registro->ruta_archivo)
                                <button class="btn btn-primary btn-sm btn-imprimir-pdf"
                                    data-filename="{{ basename($registro->ruta_archivo) }}" title="Imprimir PDF">
                                    <i class="fas fa-print"></i> Imprimir
                                </button>
                                @else
                                <span class="text-muted">Sin archivo</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('generarForm');

        // Event listener para generar parte delantera
        const btnDelante = document.getElementById('generar_delante');

        if (btnDelante) {
            btnDelante.addEventListener('click', async function (e) {
                e.preventDefault();

                const pisoSelect = document.getElementById('piso_seleccionado');
                const cantidadSelect = document.getElementById('cantidad_carnets');

                const piso = pisoSelect?.value;
                const cantidad = cantidadSelect?.value;

                if (!piso || !cantidad) {
                    alert('Por favor, seleccione el piso y la cantidad de carnets');
                    return;
                }

                const originalText = btnDelante.innerHTML;
                btnDelante.disabled = true;
                btnDelante.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando...';

                try {
                    await generarPDFCarnetDelante(piso, cantidad);
                    alert(`Se generó la parte delantera para ${cantidad} carnet(s) exitosamente`);
                } catch (error) {
                    console.error('Error generando parte delantera:', error);
                    alert('Error al generar la parte delantera: ' + error.message);
                } finally {
                    btnDelante.disabled = false;
                    btnDelante.innerHTML = originalText;
                }
            });
        }

        // Event listener para generar parte trasera
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const pisoSelect = document.getElementById('piso_seleccionado');
            const cantidadSelect = document.getElementById('cantidad_carnets');

            const piso = pisoSelect?.value;
            const cantidad = cantidadSelect?.value;

            if (!piso || !cantidad) {
                alert('Por favor, seleccione el piso y la cantidad de carnets');
                return;
            }

            const btnGenerar = form.querySelector('button[type="submit"]');
            const originalText = btnGenerar.innerHTML;
            btnGenerar.disabled = true;
            btnGenerar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando...';

            try {
                const response = await fetch('{{ route("admin.carnets.visitantes.generar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: `piso_seleccionado=${encodeURIComponent(piso)}&cantidad_carnets=${encodeURIComponent(cantidad)}`
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.error || 'Error generando carnets');
                }

                // Generar PDF con todos los carnets y guardar en servidor
                await generarPDFCarnets(data.carnets, data.carnets_ids, data.piso, data.pdf_filename);

                // Recargar página para mostrar nuevos registros
                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
                alert('Error al generar carnets: ' + error.message);
            } finally {
                btnGenerar.disabled = false;
                btnGenerar.innerHTML = originalText;
            }
        });

        // Inicializar DataTable
        $('#dataTableHistorial').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            // Por ID numérico: el texto "#119" ordenaba como cadena y mezclaba páginas; dd/mm/yyyy tampoco ordena bien por fecha
            order: [[0, 'desc']],
            columnDefs: [
                { targets: 5, orderable: false }
            ],
            pageLength: 10
        });

        // Manejar click en botón de imprimir PDF
        $(document).on('click', '.btn-imprimir-pdf', function (e) {
            e.preventDefault();
            var filename = $(this).data('filename');

            if (!filename || filename === '') {
                alert('No se encontró el nombre del archivo PDF');
                return;
            }

            // Abrir ventana en blanco
            var win = window.open('', '_blank');
            if (!win) {
                alert('Permite ventanas emergentes para visualizar el PDF.');
                return;
            }

            try {
                win.document.title = 'Imprimiendo ' + filename;
                win.document.body.innerHTML = '<p style="font-family: sans-serif; padding: 16px;">Cargando PDF...</p>';
            } catch (e) {
                console.warn('No se pudo preparar la ventana:', e);
            }

            // Solicitar el PDF en base64
            $.ajax({
                url: '{{ route("admin.carnets.visitantes.pdf") }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { filename: filename },
                success: function (resp) {
                    if (!resp || !resp.success) {
                        var msg = (resp && resp.error) ? resp.error : 'Error desconocido obteniendo PDF';
                        try {
                            win.document.body.innerHTML = '<p style="font-family: sans-serif; padding: 16px; color: #b00020;">' +
                                ('No se pudo obtener el PDF: ' + msg) + '</p>';
                        } catch (_) { }
                        return;
                    }

                    try {
                        var dataUri = resp.data_uri;
                        var raw = '';
                        if (dataUri && typeof dataUri === 'string') {
                            raw = dataUri.substring(dataUri.lastIndexOf(',') + 1);
                        } else {
                            raw = resp.base64 || '';
                        }

                        // Limpiar base64
                        raw = (raw || "").trim();
                        if (raw.startsWith("data:")) {
                            raw = raw.substring(raw.indexOf(";base64,") + 8);
                        }
                        raw = raw.replace(/[\r\n\t ]+/g, "");
                        raw = raw.replace(/%2B/gi, "+");

                        var byteCharacters = atob(raw);
                        var byteNumbers = new Array(byteCharacters.length);
                        for (var i = 0; i < byteCharacters.length; i++) {
                            byteNumbers[i] = byteCharacters.charCodeAt(i);
                        }
                        var byteArray = new Uint8Array(byteNumbers);
                        var blob = new Blob([byteArray], { type: 'application/pdf' });
                        var url = URL.createObjectURL(blob);

                        try { win.document.title = filename; } catch (_) { }
                        try {
                            win.document.body.style = 'margin:0;';
                            win.document.body.innerHTML = '<iframe src="' + url + '" title="' +
                                filename.replace(/"/g, '&quot;') +
                                '" style="border:0;position:fixed;inset:0;width:100%;height:100%"></iframe>';
                        } catch (_) {
                            win.location.href = url;
                        }
                    } catch (err) {
                        console.error('Error mostrando PDF:', err);
                        try {
                            win.document.body.innerHTML = '<p style="font-family: sans-serif; padding: 16px; color: #b00020;">' +
                                ('Error mostrando el PDF: ' + err.message) + '</p>';
                        } catch (_) { }
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    try {
                        win.document.body.innerHTML = '<p style="font-family: sans-serif; padding: 16px; color: #b00020;">' +
                            'Error de comunicación con el servidor al obtener el PDF.</p>';
                    } catch (_) { }
                }
            });
        });
    });

    // Funciones para generar PDFs (copiadas del código original)
    function loadImage(src) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = src;
        });
    }

    async function generarPDFCarnets(carnets, carnetsIds, piso, pdfFilename) {
        try {
            // Verificar jsPDF
            if (typeof jsPDF === 'undefined' && typeof window.jspdf === 'undefined') {
                throw new Error('jsPDF no está disponible');
            }

            // Cargar la plantilla
            const TEMPLATE_PATH = '{{ asset("img/templates/carnet_visitante_atras1.jpg") }}';
            const templateImg = await loadImage(TEMPLATE_PATH);

            // Dimensiones del PDF
            const pageWidth = 85;
            const pageHeight = 55;

            // Crear PDF
            let PDFConstructor = typeof jsPDF !== 'undefined' ? jsPDF : window.jspdf.jsPDF;
            const pdf = new PDFConstructor({
                orientation: 'landscape',
                unit: 'mm',
                format: [pageWidth, pageHeight],
                compress: true
            });

            // Procesar cada carnet
            for (let i = 0; i < carnets.length; i++) {
                const carnet = carnets[i];

                if (i > 0) {
                    pdf.addPage([pageWidth, pageHeight], 'landscape');
                }

                // Crear canvas
                const canvas = document.createElement('canvas');
                canvas.width = templateImg.naturalWidth;
                canvas.height = templateImg.naturalHeight;
                const ctx = canvas.getContext('2d');
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';

                // Dibujar plantilla
                ctx.drawImage(templateImg, 0, 0, canvas.width, canvas.height);

                // Cargar y dibujar QR
                const qrImg = await loadImage(carnet.qr_base64);
                const qrSize = Math.floor(canvas.width * 0.30);
                const qrX = Math.floor((canvas.width - qrSize) / 2);
                const qrY = canvas.height - qrSize - Math.floor(canvas.height * 0.08);
                ctx.drawImage(qrImg, qrX, qrY, qrSize, qrSize);

                // Agregar código del carnet
                ctx.font = 'bold 60px Arial';
                ctx.fillStyle = '#000000';
                ctx.textAlign = 'left';
                ctx.textBaseline = 'top';
                const textX = Math.floor(canvas.width * 0.08);
                const textY = Math.floor(canvas.height * 0.08);
                ctx.fillText(carnet.codigo, textX, textY);

                // Rotar para PDF
                const pdfCanvas = document.createElement('canvas');
                pdfCanvas.width = 850;
                pdfCanvas.height = Math.round(850 / (85 / 55));
                const pdfCtx = pdfCanvas.getContext('2d');
                pdfCtx.imageSmoothingEnabled = true;
                pdfCtx.imageSmoothingQuality = 'high';

                pdfCtx.save();
                pdfCtx.translate(pdfCanvas.width / 2, pdfCanvas.height / 2);
                pdfCtx.rotate(Math.PI / 2);

                const scaleX = pdfCanvas.width / canvas.height;
                const scaleY = pdfCanvas.height / canvas.width;
                const scale = Math.max(scaleX, scaleY) * 1.01;

                pdfCtx.drawImage(
                    canvas,
                    -canvas.width * scale / 2,
                    -canvas.height * scale / 2,
                    canvas.width * scale,
                    canvas.height * scale
                );
                pdfCtx.restore();

                const rotatedData = pdfCanvas.toDataURL('image/png');
                const oversize = 0.1;
                pdf.addImage(
                    rotatedData,
                    'PNG',
                    -oversize,
                    -oversize,
                    pageWidth + (oversize * 2),
                    pageHeight + (oversize * 2),
                    undefined,
                    'FAST'
                );
            }

            // Obtener PDF como base64
            const pdfDataUri = pdf.output('datauristring');
            const pdfBase64 = pdfDataUri.includes(',') ? pdfDataUri.split(',')[1] : pdfDataUri.replace(/^data:application\/pdf;base64,/, '');

            // Guardar en servidor
            try {
                const saveResponse = await fetch('{{ route("admin.carnets.visitantes.guardar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: `pdf_base64=${encodeURIComponent(pdfBase64)}&carnets_ids=${encodeURIComponent(JSON.stringify(carnetsIds))}&piso=${encodeURIComponent(piso)}&filename=${encodeURIComponent(pdfFilename)}`
                });

                const saveData = await saveResponse.json();
                if (!saveData.success) {
                    console.error('Error guardando PDF:', saveData.error);
                }
            } catch (saveError) {
                console.error('Error al guardar PDF:', saveError);
            }

            // Imprimir
            pdf.autoPrint();
            const pdfBlob = pdf.output('blob');
            const pdfUrl = URL.createObjectURL(pdfBlob);
            const printWindow = window.open(pdfUrl, '_blank');

            if (printWindow) {
                printWindow.onload = function () {
                    printWindow.print();
                    setTimeout(() => URL.revokeObjectURL(pdfUrl), 1000);
                };
            }

        } catch (error) {
            console.error('Error generando PDF:', error);
            throw error;
        }
    }

    async function generarPDFCarnetDelante(piso, cantidad) {
        try {
            // Verificar jsPDF
            if (typeof jsPDF === 'undefined' && typeof window.jspdf === 'undefined') {
                throw new Error('jsPDF no está disponible');
            }

            // Cargar plantilla delantera
            const TEMPLATE_PATH = '{{ asset("img/templates/carnet_visitante_delante.png") }}';
            const templateImg = await loadImage(TEMPLATE_PATH);

            // Dimensiones del PDF
            const pageWidth = 85;
            const pageHeight = 55;

            // Crear PDF
            let PDFConstructor = typeof jsPDF !== 'undefined' ? jsPDF : window.jspdf.jsPDF;
            const pdf = new PDFConstructor({
                orientation: 'landscape',
                unit: 'mm',
                format: [pageWidth, pageHeight],
                compress: true
            });

            // Procesar cada carnet
            for (let i = 0; i < parseInt(cantidad); i++) {
                if (i > 0) {
                    pdf.addPage([pageWidth, pageHeight], 'landscape');
                }

                // Crear canvas
                const canvas = document.createElement('canvas');
                canvas.width = templateImg.naturalWidth;
                canvas.height = templateImg.naturalHeight;
                const ctx = canvas.getContext('2d');
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';

                // Dibujar plantilla
                ctx.drawImage(templateImg, 0, 0, canvas.width, canvas.height);



                // Rotar para PDF (mismo proceso que la parte trasera)
                const pdfCanvas = document.createElement('canvas');
                pdfCanvas.width = 850;
                pdfCanvas.height = Math.round(850 / (85 / 55));
                const pdfCtx = pdfCanvas.getContext('2d');
                pdfCtx.imageSmoothingEnabled = true;
                pdfCtx.imageSmoothingQuality = 'high';

                pdfCtx.save();
                pdfCtx.translate(pdfCanvas.width / 2, pdfCanvas.height / 2);
                pdfCtx.rotate(Math.PI / 2);

                const scaleX = pdfCanvas.width / canvas.height;
                const scaleY = pdfCanvas.height / canvas.width;
                const scale = Math.max(scaleX, scaleY) * 1.01;

                pdfCtx.drawImage(
                    canvas,
                    -canvas.width * scale / 2,
                    -canvas.height * scale / 2,
                    canvas.width * scale,
                    canvas.height * scale
                );
                pdfCtx.restore();

                const rotatedData = pdfCanvas.toDataURL('image/png');
                const oversize = 0.1;
                pdf.addImage(
                    rotatedData,
                    'PNG',
                    -oversize,
                    -oversize,
                    pageWidth + (oversize * 2),
                    pageHeight + (oversize * 2),
                    undefined,
                    'FAST'
                );
            }

            // Imprimir
            pdf.autoPrint();
            const pdfBlob = pdf.output('blob');
            const pdfUrl = URL.createObjectURL(pdfBlob);
            const printWindow = window.open(pdfUrl, '_blank');

            if (printWindow) {
                printWindow.onload = function () {
                    printWindow.print();
                    setTimeout(() => URL.revokeObjectURL(pdfUrl), 1000);
                };
            }

        } catch (error) {
            console.error('Error generando PDF delantera:', error);
            throw error;
        }
    }
</script>
@endpush
@endsection