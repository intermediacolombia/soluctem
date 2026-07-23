<?php
include('login/sesion.php');

// Conexión a la base de datos
include('inc/config.php');

$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener las zonas del usuario desde la sesión
$zonas_usuario = $_SESSION['zonas'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularios Pendientes</title>
    <!-- Incluir Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluir DataTables CSS para Bootstrap -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Incluir Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Incluir DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <?php include('inc/header.php');?>
    <style>
        /* Estilo para el contenedor principal */
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Estilo para la tabla */
        table {
            width: 100%;
            table-layout: auto;
        }

        /* Estilo para las celdas de la tabla */
        table th, table td {
            padding: 8px;
            font-size: 14px;
        }

        /* Estilo para el encabezado de la tabla */
        thead.thead-dark th {
            font-size: 15px;
        }

        /* Estilos para hacer que las filas sean clicables */
        table tbody tr {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        /* Estilo para cambiar el color de fondo al pasar el cursor */
        table tbody tr:hover {
            background-color: #ffe8e8;
        }

        /* Color para las celdas de estado */
        .estado-pendiente {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        /* Header mejorado */
        .page-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .page-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header .subtitle {
            margin-top: 8px;
            font-size: 0.95rem;
            opacity: 0.95;
        }

        /* Badge de usuario */
        .user-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 10px;
        }

        /* Estadísticas compactas */
        .stats-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .stat-card.pendientes {
            border-left: 5px solid #dc3545;
        }

        .stat-card.hoy {
            border-left: 5px solid #ffc107;
        }

        .stat-card.semana {
            border-left: 5px solid #17a2b8;
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.7;
        }

        .stat-content .label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .stat-content .value {
            font-size: 2.2rem;
            font-weight: 700;
            color: #212529;
            line-height: 1;
        }

        /* Filtros compactos */
        .filters-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .filters-title {
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-item {
            flex: 1;
            min-width: 180px;
        }

        .filter-item label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #495057;
            margin-bottom: 5px;
            display: block;
        }

        .filter-item select,
        .filter-item input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .filter-item select:focus,
        .filter-item input:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .btn-filter {
            padding: 8px 20px;
            font-size: 0.9rem;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* Botones de acción rápida */
        .quick-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .quick-btn {
            background: white;
            border: 2px solid #dee2e6;
            color: #495057;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .quick-btn:hover {
            background: #f8f9fa;
            border-color: #007bff;
            color: #007bff;
            transform: translateY(-2px);
        }

        .quick-btn.active {
            background: #007bff;
            border-color: #007bff;
            color: white;
        }

        /* Loading overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        /* Media queries para dispositivos móviles */
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }

            table th, table td {
                padding: 6px;
                font-size: 12px;
            }

            .page-header {
                padding: 20px 15px;
            }

            .page-header h1 {
                font-size: 1.4rem;
            }

            .filter-row {
                flex-direction: column;
            }

            .filter-item {
                min-width: 100%;
            }

            .filter-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn-filter {
                width: 100%;
                justify-content: center;
            }

            .stats-bar {
                flex-direction: column;
            }

            .stat-card {
                min-width: 100%;
            }

            .quick-actions {
                flex-direction: column;
            }

            .quick-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<?php include('inc/menu.php')?>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3 text-muted">Cargando formularios...</p>
    </div>
</div>

<div class="container mt-4">
    <!-- Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-exclamation-circle"></i>
            Formularios Pendientes
        </h1>
        <div class="subtitle">
            Formularios que requieren tu atención y aprobación
        </div>
        <div class="user-badge">
            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['nombre']); ?>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-bar">
        <div class="stat-card pendientes">
            <div class="stat-header">
                <div class="stat-icon text-danger">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <div class="label">Total Pendientes</div>
                    <div class="value" id="statTotal">-</div>
                </div>
            </div>
        </div>
        <div class="stat-card hoy">
            <div class="stat-header">
                <div class="stat-icon text-warning">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <div class="label">Hoy</div>
                    <div class="value" id="statHoy">-</div>
                </div>
            </div>
        </div>
        <div class="stat-card semana">
            <div class="stat-header">
                <div class="stat-icon text-info">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-content">
                    <div class="label">Esta Semana</div>
                    <div class="value" id="statSemana">-</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Rápidos -->
    <div class="quick-actions">
        <div class="quick-btn" onclick="filterQuick('todos')">
            <i class="fas fa-list"></i> Todos
        </div>
        <div class="quick-btn" onclick="filterQuick('hoy')">
            <i class="fas fa-calendar-day"></i> Hoy
        </div>
        <div class="quick-btn" onclick="filterQuick('semana')">
            <i class="fas fa-calendar-week"></i> Esta Semana
        </div>
        <div class="quick-btn" onclick="filterQuick('mes')">
            <i class="fas fa-calendar-alt"></i> Este Mes
        </div>
    </div>

    <!-- Filtros Detallados -->
    <div class="filters-section">
        <div class="filters-title">
            <i class="fas fa-filter"></i> Filtros Avanzados
        </div>
        <div class="filter-row">
            <div class="filter-item">
                <label><i class="fas fa-calendar"></i> Fecha Inicio</label>
                <input type="date" class="form-control" id="filterFechaInicio">
            </div>
            <div class="filter-item">
                <label><i class="fas fa-calendar"></i> Fecha Fin</label>
                <input type="date" class="form-control" id="filterFechaFin">
            </div>
            <div class="filter-item">
                <label><i class="fas fa-store"></i> Tienda</label>
                <select class="form-control" id="filterTienda">
                    <option value="">Todas las tiendas</option>
                </select>
            </div>
            <div class="filter-item">
                <label><i class="fas fa-user-cog"></i> Técnico</label>
                <select class="form-control" id="filterTecnico">
                    <option value="">Todos los técnicos</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="button" class="btn btn-primary btn-filter" onclick="applyFilters()">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <button type="button" class="btn btn-secondary btn-filter" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla de datos -->
    <div class="table-responsive">
        <table id="formularios" class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Técnico</th>
                    <th>Tienda</th>
                    <th>Ticket</th>
                    <th>Fecha</th>
                    <th>Departamento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargarán dinámicamente vía AJAX -->
            </tbody>
        </table>
    </div>
</div>

<script>
// Variables globales
let table;
let activeQuickFilter = 'todos';

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    initializeDataTable();
    loadFilterOptions();
    loadStats();
});

// Inicializar DataTable
function initializeDataTable() {
    table = $('#formularios').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get_pendientes.php',
            type: 'POST',
            data: function(d) {
                d.filterFechaInicio = $('#filterFechaInicio').val();
                d.filterFechaFin = $('#filterFechaFin').val();
                d.filterTienda = $('#filterTienda').val();
                d.filterTecnico = $('#filterTecnico').val();
                d.quickFilter = activeQuickFilter;
            },
            beforeSend: function() {
                $('#loadingOverlay').addClass('active');
            },
            complete: function() {
                $('#loadingOverlay').removeClass('active');
            },
            error: function(xhr, error, thrown) {
                console.error('Error cargando datos:', error);
                alert('Error al cargar los datos. Por favor, intenta de nuevo.');
            }
        },
        columns: [
            { data: 'id' },
            { data: 'nombreSolicitante' },
            { data: 'nombreTienda' },
            { data: 'numeroTicket' },
            { data: 'fecha' },
            { data: 'departamento' },
            { 
                data: 'estado',
                render: function(data, type, row) {
                    return '<span class="estado-pendiente">Pendiente</span>';
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        createdRow: function(row, data, dataIndex) {
            $(row).attr('data-href', '/admin/form/?id=' + data.id);
            $(row).css('cursor', 'pointer');
        },
        drawCallback: function(settings) {
            loadStats();
        }
    });

    // Hacer las filas clicables
    $('#formularios tbody').on('click', 'tr', function() {
        var href = $(this).data('href');
        if(href) {
            window.location.href = href;
        }
    });
}

// Cargar opciones de filtros
function loadFilterOptions() {
    $.ajax({
        url: 'get_filter_options_pendientes.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if(data.tiendas) {
                $('#filterTienda').empty().append('<option value="">Todas las tiendas</option>');
                data.tiendas.forEach(function(tienda) {
                    $('#filterTienda').append('<option value="' + tienda + '">' + tienda + '</option>');
                });
            }

            if(data.tecnicos) {
                $('#filterTecnico').empty().append('<option value="">Todos los técnicos</option>');
                data.tecnicos.forEach(function(tecnico) {
                    $('#filterTecnico').append('<option value="' + tecnico + '">' + tecnico + '</option>');
                });
            }
        },
        error: function() {
            console.error('Error cargando opciones de filtros');
        }
    });
}

// Cargar estadísticas
function loadStats() {
    $.ajax({
        url: 'get_stats_pendientes.php',
        type: 'POST',
        data: {
            filterFechaInicio: $('#filterFechaInicio').val(),
            filterFechaFin: $('#filterFechaFin').val(),
            filterTienda: $('#filterTienda').val(),
            filterTecnico: $('#filterTecnico').val()
        },
        dataType: 'json',
        success: function(data) {
            $('#statTotal').text(data.total || 0);
            $('#statHoy').text(data.hoy || 0);
            $('#statSemana').text(data.semana || 0);
        },
        error: function() {
            console.error('Error cargando estadísticas');
        }
    });
}

// Aplicar filtros
function applyFilters() {
    activeQuickFilter = 'custom';
    $('.quick-btn').removeClass('active');
    table.ajax.reload();
}

// Limpiar filtros
function clearFilters() {
    $('#filterFechaInicio').val('');
    $('#filterFechaFin').val('');
    $('#filterTienda').val('');
    $('#filterTecnico').val('');
    activeQuickFilter = 'todos';
    $('.quick-btn').removeClass('active');
    $('.quick-btn').first().addClass('active');
    table.ajax.reload();
}

// Filtros rápidos
function filterQuick(tipo) {
    activeQuickFilter = tipo;
    
    // Limpiar filtros detallados
    $('#filterFechaInicio').val('');
    $('#filterFechaFin').val('');
    $('#filterTienda').val('');
    $('#filterTecnico').val('');
    
    // Actualizar botón activo
    $('.quick-btn').removeClass('active');
    event.currentTarget.classList.add('active');
    
    table.ajax.reload();
}
</script>

<?php include('inc/menu-foot.php');?>
<?php include('inc/footer.php');?>
</body>
</html>

<?php
$conexion->close();
?>







