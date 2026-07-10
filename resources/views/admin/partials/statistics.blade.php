<!-- Statistics Dashboard Header -->
<div class="text-center mb-4">
    <h2 class="h3 text-gray-800 font-weight-bold">Tablero de Control de Métricas</h2>
    <p class="text-muted">Visualización en tiempo real de los indicadores clave de rendimiento</p>
</div>

<!-- Recepción: Tráfico y Visitantes -->
<section class="mb-5">
    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
        <i class="fas fa-building text-warning mr-2"></i>
        <h3 class="h5 font-weight-bold text-gray-800 mb-0">Recepción: Tráfico y Visitantes</h3>
    </div>
    <div class="row">
        <!-- Visitors Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h4 class="h6 font-weight-bold text-gray-700 mb-3">Visitantes por Piso (Análisis Temporal)</h4>
                    
                    <!-- Filters -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-primary" id="btn-filter-today" onclick="setFilter('today')">Hoy</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-filter-week" onclick="setFilter('week')">Esta Semana</button>
                        </div>
                        <div class="form-inline">
                            <select class="form-control form-control-sm mr-2" id="filter-month" onchange="checkCustomFilter()">
                                <option value="" disabled selected>Mes</option>
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                            <select class="form-control form-control-sm" id="filter-year" onchange="checkCustomFilter()">
                                <option value="" disabled selected>Año</option>
                                @for($i = date('Y'); $i >= 2024; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-3">
                        <div class="col-6 col-md-3 mb-2">
                            <div class="text-center p-2 bg-light rounded border">
                                <small class="text-muted d-block">Hoy</small>
                                <span class="h5 font-weight-bold text-warning mb-0" id="visitors-today">-</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <div class="text-center p-2 bg-light rounded border">
                                <small class="text-muted d-block">Esta Semana</small>
                                <span class="h5 font-weight-bold text-primary mb-0" id="visitors-week">-</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <div class="text-center p-2 bg-light rounded border">
                                <small class="text-muted d-block">Este Mes</small>
                                <span class="h5 font-weight-bold text-purple mb-0" id="visitors-month">-</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <div class="text-center p-2 bg-light rounded border">
                                <small class="text-muted d-block">Total Histórico</small>
                                <span class="h5 font-weight-bold text-gray-700 mb-0" id="visitors-total">-</span>
                            </div>
                        </div>
                    </div>
                    <div style="height: 250px;">
                        <canvas id="chartVisitorsFloor"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Average Time -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h4 class="h6 font-weight-bold text-gray-700 mb-3">Tiempo Promedio de Visita (min)</h4>
                    <div style="height: 200px;">
                        <canvas id="chartAvgTime"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <span class="display-4 font-weight-bold text-warning" id="avg-time-general">--</span>
                        <br>
                        <span class="display-4 font-weight-bold text-warning">O</span>
                        <br>
                        <span class="display-4 font-weight-bold text-warning" id="avg-time-general-h">--</span>
                        
                        <p class="small text-muted mb-0">Promedio General</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rotación: Reasignaciones de Personal -->
<section class="mb-5">
    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
        <i class="fas fa-sync-alt text-info mr-2"></i>
        <h3 class="h5 font-weight-bold text-gray-800 mb-0">Rotación: Reasignaciones de Personal</h3>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Operador</th>
                            <th class="text-center">Esta Semana</th>
                            <th class="text-center">Este Mes</th>
                            <th class="text-right">Total Histórico</th>
                            <th class="text-right">Tendencia</th>
                        </tr>
                    </thead>
                    <tbody id="rotation-table-body">
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- RRHH: Gestión de Carnets y Personal -->
<section class="mb-5">
    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
        <i class="fas fa-id-card text-primary mr-2"></i>
        <h3 class="h5 font-weight-bold text-gray-800 mb-0">RRHH: Gestión de Carnets y Personal</h3>
    </div>
    
    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Total Activos -->
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card border-left-primary shadow h-100 py-2 cursor-pointer card-hover-effect" onclick="openCardDetail('activos')" title="Clic para ver detalle">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rrhh-activos">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sin Asignación (Antes Inactivos) -->
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card border-left-danger shadow h-100 py-2 cursor-pointer card-hover-effect" onclick="openCardDetail('sin_asignacion')" title="Clic para ver detalle">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Sin Asignación</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rrhh-inactivos">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sin Carnet -->
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card border-left-warning shadow h-100 py-2 cursor-pointer card-hover-effect" onclick="openCardDetail('sin_carnet')" title="Clic para ver detalle">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sin Carnet</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rrhh-sin-carnet">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Preasignados (Antes Datos Modificados) -->
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2 cursor-pointer card-hover-effect" onclick="openCardDetail('preasignados')" title="Clic para ver detalle">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Preasignados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rrhh-modificados">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h4 class="h6 font-weight-bold text-gray-700 mb-3">Estado de Carnetización</h4>
                    <div style="height: 250px;">
                        <canvas id="chartCarnetStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h4 class="h6 font-weight-bold text-gray-700 mb-3">Emisión de Carnets (Últimos 6 meses)</h4>
                    <div style="height: 250px;">
                        <canvas id="chartCarnetHistory"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<div class="card shadow-sm">
    <div class="card-body py-2 px-3">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                <i class="fas fa-clock mr-1"></i>
                <span id="last-update">Última actualización: --</span>
            </small>
            <button class="btn btn-sm btn-outline-primary" onclick="refreshStatistics()">
                <i class="fas fa-sync-alt mr-1"></i>Actualizar
            </button>
        </div>
    </div>
</div>

<!-- Modal para Detalle de Tarjetas -->
<div class="modal fade" id="cardDetailModal" tabindex="-1" role="dialog" aria-labelledby="cardDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="cardDetailModalLabel">Detalle de Registros</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6 offset-md-6">
                        <div class="input-group">
                            <input type="text" id="cardDetailSearch" class="form-control" placeholder="Buscar por cédula, nombre o código..." oninput="debounceCardDetailSearch()">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" onclick="loadCardDetailData(1)">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="cardDetailTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr id="cardDetailTableHeader">
                                <!-- Las cabeceras se inyectan mediante JS según el tipo -->
                            </tr>
                        </thead>
                        <tbody id="cardDetailTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-4">Cargando datos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-sm text-muted" id="cardDetailPaginationInfo">
                        Mostrando registros...
                    </div>
                    <ul class="pagination pagination-sm mb-0" id="cardDetailPagination">
                        <!-- Botones se inyectan mediante JS -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .card-hover-effect:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: all .2s ease;
    }
</style>

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
// Chart instances
let chartVisitors, chartAvgTime, chartCarnetStatus, chartCarnetHistory;
let currentFilter = { period: 'today', month: null, year: null };

// Load statistics on page load
document.addEventListener('DOMContentLoaded', function() {
    // Only load if on statistics view
    if (document.getElementById('chartVisitorsFloor')) {
        loadStatistics();
    }
});

// Function to set filter
function setFilter(period) {
    currentFilter = { period: period, month: null, year: null };
    
    // Update UI
    document.getElementById('btn-filter-today').className = period === 'today' ? 'btn btn-primary' : 'btn btn-outline-primary';
    document.getElementById('btn-filter-week').className = period === 'week' ? 'btn btn-primary' : 'btn btn-outline-primary';
    document.getElementById('filter-month').value = "";
    document.getElementById('filter-year').value = "";
    
    loadStatistics();
}

// Function to check custom filter
function checkCustomFilter() {
    const month = document.getElementById('filter-month').value;
    const year = document.getElementById('filter-year').value;
    
    if (month && year) {
        currentFilter = { period: 'custom', month: month, year: year };
        
        // Update UI
        document.getElementById('btn-filter-today').className = 'btn btn-outline-primary';
        document.getElementById('btn-filter-week').className = 'btn btn-outline-primary';
        
        loadStatistics();
    }
}

// Function to load statistics data
function loadStatistics() {
    const url = new URL('{{ route('admin.stats.data') }}', window.location.origin);
    url.searchParams.append('period', currentFilter.period);
    if (currentFilter.month) url.searchParams.append('month', currentFilter.month);
    if (currentFilter.year) url.searchParams.append('year', currentFilter.year);

    fetch(url)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                updateStatistics(result.data);
                updateTimestamp(result.data.timestamp);
            } else {
                console.error('Error loading statistics:', result.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Function to refresh statistics
function refreshStatistics() {
    loadStatistics();
}

// Update all statistics
function updateStatistics(data) {
    updateRecepcionStats(data.recepcion);
    updateRotacionStats(data.rotacion);
    updateRRHHStats(data.rrhh);
}

// Update Recepción statistics
function updateRecepcionStats(recepcion) {
    const floors = recepcion.floors || ['PB', '1', '2', '3', '4', '5'];
    const chartData = [];
    const weekData = []; // Keep for comparison if needed, or remove
    let totalToday = 0, totalWeek = 0, totalMonth = 0, totalAll = 0;

    floors.forEach(floor => {
        const floorData = recepcion.visitors_by_floor[floor] || {today: 0, this_week: 0, this_month: 0, total: 0, filtered: 0};
        
        // Use filtered count for the chart
        chartData.push(floorData.filtered);
        
        // Accumulate totals for summary cards
        totalToday += floorData.today;
        totalWeek += floorData.this_week;
        totalMonth += floorData.this_month;
        totalAll += floorData.total;
    });

    // Update summary cards
    document.getElementById('visitors-today').textContent = totalToday;
    document.getElementById('visitors-week').textContent = totalWeek.toLocaleString();
    document.getElementById('visitors-month').textContent = totalMonth.toLocaleString();
    document.getElementById('visitors-total').textContent = totalAll >= 1000 ? (totalAll / 1000).toFixed(1) + 'k' : totalAll;

    // Determine label based on filter
    let chartLabel = 'Visitantes';
    if (currentFilter.period === 'today') chartLabel = 'Hoy';
    else if (currentFilter.period === 'week') chartLabel = 'Esta Semana';
    else if (currentFilter.period === 'custom') chartLabel = `Mes ${currentFilter.month}/${currentFilter.year}`;

    // Update visitors chart
    if (chartVisitors) chartVisitors.destroy();
    const ctxVisitors = document.getElementById('chartVisitorsFloor').getContext('2d');
    chartVisitors = new Chart(ctxVisitors, {
        type: 'bar',
        data: {
            labels: floors.map(f => f === 'PB' ? 'PB' : 'Piso ' + f),
            datasets: [
                {
                    label: chartLabel,
                    data: chartData,
                    backgroundColor: '#f6c23e',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Update average time
    const avgTime = recepcion.avg_visit_time;
    document.getElementById('avg-time-general').textContent = Math.round(avgTime.general) + 'min';
    document.getElementById('avg-time-general-h').textContent = Math.round(avgTime.general / 60) + 'h';
    
    // Average time by floor chart
    if (chartAvgTime) chartAvgTime.destroy();
    const ctxAvgTime = document.getElementById('chartAvgTime').getContext('2d');
    
    // Generate colors dynamically
    const colors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796',
        '#5a5c69', '#f8f9fc', '#2e59d9', '#17a673', '#2c9faf', '#e83e8c',
        '#fd7e14', '#6f42c1', '#20c9a6', '#ffc107', '#dc3545', '#007bff',
        '#6610f2', '#6c757d', '#28a745', '#17a2b8', '#ffc107', '#28a745',
        '#20c997', '#17a2b8', '#6c757d', '#343a40', '#007bff', '#6610f2',
        '#6f42c1', '#e83e8c', '#dc3545', '#fd7e14', '#ffc107'
    ];
    
    chartAvgTime = new Chart(ctxAvgTime, {
        type: 'doughnut',
        data: {
            labels: floors.map(f => f.includes('JC') ? f : (f.startsWith('MZ') ? f : 'Piso ' + f)),
            datasets: [{
                data: floors.map(f => avgTime.by_floor[f] || 0),
                backgroundColor: colors.slice(0, floors.length),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right', // Move legend to right for better visibility
                    labels: {
                        boxWidth: 10,
                        font: {
                            size: 9
                        },
                        padding: 8
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + ' min';
                        }
                    }
                }
            },
            layout: {
                padding: {
                    left: 0,
                    right: 0,
                    top: 0,
                    bottom: 0
                }
            }
        }
    });
}

// Update Rotación statistics
function updateRotacionStats(rotacion) {
    const tbody = document.getElementById('rotation-table-body');
    
    if (!rotacion.operators || rotacion.operators.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay datos de operadores disponibles</td></tr>';
        return;
    }

    tbody.innerHTML = rotacion.operators.map(op => {
        const trendIcon = op.trend === 'up' ? '<i class="fas fa-arrow-up text-success"></i>' :
                          op.trend === 'down' ? '<i class="fas fa-arrow-down text-danger"></i>' :
                          '<i class="fas fa-minus text-muted"></i>';
        
        return `
            <tr>
                <td class="font-weight-bold">${op.nombre}</td>
                <td class="text-center"><span class="badge badge-primary">${op.this_week}</span></td>
                <td class="text-center">${op.this_month}</td>
                <td class="text-right font-weight-bold">${op.total}</td>
                <td class="text-right">${trendIcon}</td>
            </tr>
        `;
    }).join('');
}

// Update RRHH statistics
function updateRRHHStats(rrhh) {
    document.getElementById('rrhh-activos').textContent = rrhh.total_activos.toLocaleString();
    document.getElementById('rrhh-inactivos').textContent = rrhh.sin_asignacion.toLocaleString();
    document.getElementById('rrhh-sin-carnet').textContent = rrhh.sin_carnet.toLocaleString();
    document.getElementById('rrhh-modificados').textContent = rrhh.preasignados.toLocaleString();

    // Carnet status chart
    if (chartCarnetStatus) chartCarnetStatus.destroy();
    const ctxCarnetStatus = document.getElementById('chartCarnetStatus').getContext('2d');
    chartCarnetStatus = new Chart(ctxCarnetStatus, {
        type: 'doughnut',
        data: {
            labels: ['Activos', 'Sin Asignación', 'Sin Carnet', 'Preasignados'],
            datasets: [{
                data: [
                    rrhh.carnet_status.activos,
                    rrhh.carnet_status.sin_asignacion,
                    rrhh.carnet_status.sin_carnet,
                    rrhh.carnet_status.preasignados
                ],
                backgroundColor: ['#4e73df', '#e74a3b', '#f6c23e', '#36b9cc'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Carnet history chart
    if (chartCarnetHistory) chartCarnetHistory.destroy();
    const ctxCarnetHistory = document.getElementById('chartCarnetHistory').getContext('2d');
    chartCarnetHistory = new Chart(ctxCarnetHistory, {
        type: 'line',
        data: {
            labels: rrhh.carnets_por_mes.map(m => m.month),
            datasets: [{
                label: 'Carnets Emitidos',
                data: rrhh.carnets_por_mes.map(m => m.count),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4e73df',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Update timestamp
function updateTimestamp(timestamp) {
    const date = new Date(timestamp);
    const timeString = date.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
    document.getElementById('last-update').textContent = `Última actualización: ${timeString}`;
}

// ==========================================
// Modal de Detalle de Tarjetas RRHH
// ==========================================
let currentCardType = '';

function openCardDetail(type) {
    currentCardType = type;
    document.getElementById('cardDetailSearch').value = '';
    
    // Mover el modal al body si no está allí (Para solucionar el problema del flickering / overlay)
    if ($('#cardDetailModal').parent().prop('tagName') !== 'BODY') {
        $('#cardDetailModal').appendTo('body');
    }
    
    // Configurar título y cabeceras según el tipo
    const titles = {
        'activos': 'Detalle: Total Activos (Asignaciones completas)',
        'sin_asignacion': 'Detalle: Sin Asignación (Carnets sin entregar)',
        'sin_carnet': 'Detalle: Sin Carnet (Empleados sin carnet)',
        'preasignados': 'Detalle: Preasignados (Esperando entrega)'
    };
    
    document.getElementById('cardDetailModalLabel').textContent = titles[type];
    
    // Inicializar cabeceras según el tipo
    let headersHTML = '';
    if (type === 'activos') {
        headersHTML = `
            <th>Cédula</th>
            <th>Nombre Completo</th>
            <th>Código de Carnet</th>
            <th>Fecha Asignación</th>
        `;
    } else if (type === 'sin_asignacion') {
        headersHTML = `
            <th>Cédula Preasignada</th>
            <th>Nombre Preasignado</th>
            <th>Código de Carnet</th>
            <th>Fecha Creación</th>
        `;
    } else if (type === 'sin_carnet') {
        headersHTML = `
            <th>Cédula</th>
            <th>Nombre Completo</th>
            <th>Estado</th>
            <th>Fecha Ingreso</th>
        `;
    } else if (type === 'preasignados') {
        headersHTML = `
            <th>Cédula</th>
            <th>Nombre Completo</th>
            <th>Código de Carnet</th>
            <th>Preasignado por</th>
        `;
    }
    
    document.getElementById('cardDetailTableHeader').innerHTML = headersHTML;
    
    // Mostrar modal
    $('#cardDetailModal').modal('show');
    
    // Cargar datos
    loadCardDetailData(1);
}

let debounceTimer;
function debounceCardDetailSearch() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        loadCardDetailData(1);
    }, 400); // 400ms delay para no saturar el servidor
}

function loadCardDetailData(page) {
    const search = document.getElementById('cardDetailSearch').value;
    const tbody = document.getElementById('cardDetailTableBody');
    
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando datos...</td></tr>';
    
    const url = new URL('{{ route('admin.stats.card-detail') }}', window.location.origin);
    url.searchParams.append('type', currentCardType);
    url.searchParams.append('search', search);
    url.searchParams.append('page', page);

    fetch(url)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                renderCardDetailTable(result.data.records, currentCardType);
                renderCardDetailPagination(result.data.page, result.data.total_pages, result.data.total);
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Error: ${result.error || 'No se pudieron cargar los datos'}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error fetching card detail:', error);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error de conexión al cargar datos.</td></tr>';
        });
}

function renderCardDetailTable(records, type) {
    const tbody = document.getElementById('cardDetailTableBody');
    
    if (!records || records.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No se encontraron registros</td></tr>';
        return;
    }
    
    let html = '';
    records.forEach(row => {
        html += '<tr>';
        
        if (type === 'activos') {
            html += `
                <td>${row.cedula}</td>
                <td class="font-weight-bold">${row.nombre_completo}</td>
                <td><span class="badge badge-primary">${row.codigo_carnet}</span></td>
                <td>${new Date(row.fecha_asignacion).toLocaleDateString('es-ES')}</td>
            `;
        } else if (type === 'sin_asignacion') {
            html += `
                <td>${row.cedula}</td>
                <td class="font-weight-bold">${row.nombre_completo}</td>
                <td><span class="badge badge-secondary">${row.codigo_carnet}</span></td>
                <td>${new Date(row.fecha_creacion).toLocaleDateString('es-ES')}</td>
            `;
        } else if (type === 'sin_carnet') {
            html += `
                <td>${row.cedula}</td>
                <td class="font-weight-bold">${row.nombre_completo}</td>
                <td><span class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Sin asignar</span></td>
                <td>${new Date(row.fecha_registro).toLocaleDateString('es-ES')}</td>
            `;
        } else if (type === 'preasignados') {
            const numeroFisica = row.numero_tarjeta_fisica ? ` <small class="text-muted">(${row.numero_tarjeta_fisica})</small>` : '';
            html += `
                <td>${row.cedula}</td>
                <td class="font-weight-bold">${row.nombre_completo}</td>
                <td><span class="badge badge-info">${row.codigo_carnet}</span>${numeroFisica}</td>
                <td><small class="badge badge-light border">${row.preasignado_por || 'N/A'}</small></td>
            `;
        }
        
        html += '</tr>';
    });
    
    tbody.innerHTML = html;
}

function renderCardDetailPagination(currentPage, totalPages, totalRecords) {
    document.getElementById('cardDetailPaginationInfo').textContent = `Mostrando ${totalRecords} registros en total`;
    
    const ul = document.getElementById('cardDetailPagination');
    if (totalPages <= 1) {
        ul.innerHTML = '';
        return;
    }
    
    let html = '';
    
    // Prev
    if (currentPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); loadCardDetailData(${currentPage - 1})">&laquo;</a></li>`;
    } else {
        html += `<li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>`;
    }
    
    // Pages (simplificada: primera, prev, actual, sig, ult)
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); loadCardDetailData(1)">1</a></li>`;
        if (startPage > 2) html += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); loadCardDetailData(${i})">${i}</a></li>`;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) html += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
        html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); loadCardDetailData(${totalPages})">${totalPages}</a></li>`;
    }
    
    // Next
    if (currentPage < totalPages) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); loadCardDetailData(${currentPage + 1})">&raquo;</a></li>`;
    } else {
        html += `<li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>`;
    }
    
    ul.innerHTML = html;
}
</script>
@endpush
