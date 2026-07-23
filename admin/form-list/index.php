<?php include('../login/sesion.php'); ?>
<?php
// Conexión a la base de datos
include('../inc/config.php');

$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener las zonas del usuario desde la sesión
$zonas_usuario = $_SESSION['zonas']; // Esto es una cadena con las zonas separadas por comas
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Formularios</title>
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
    <!-- Incluir DataTables JS para Bootstrap -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <style>
        /* Estilos para hacer que las filas sean clicables */
        table tbody tr {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        /* Estilo para cambiar el color de fondo al pasar el cursor */
        table tbody tr:hover {
            background-color: #e8f4f8;
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
        .estado-aprobado {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
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

        /* Filtros compactos */
        .filters-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .filter-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-item {
            flex: 1;
            min-width: 200px;
        }

        .filter-item label {
            font-weight: 600;
            font-size: 0.9rem;
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
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* Estadísticas compactas */
        .stats-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .stat-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 180px;
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.7;
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #212529;
            line-height: 1;
        }

        /* Header mejorado */
        .page-title {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #007bff;
        }

        .page-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #212529;
            margin: 0;
        }

        /* Botones de acción */
        .action-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
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
            }

            .stats-bar {
                flex-direction: column;
            }

            .stat-item {
                min-width: 100%;
            }
        }

        /* Loading */
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
    </style>
    <?php include('../inc/header.php');?>
</head>
<body>
<?php include('../inc/menu.php')?>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3 text-muted">Cargando datos...</p>
    </div>
</div>

<!-- Mensajes de alerta -->
<div class="container mt-4">
    <?php if (isset($_SESSION['delete_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['delete_success']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['delete_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['delete_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['delete_error']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['delete_error']); ?>
    <?php endif; ?>
</div>

<!-- Contenedor principal -->
<div class="container mt-4">
    <!-- Header -->
    <div class="page-title">
        <h1><i class="fas fa-clipboard-list"></i> Formularios de Mantenimiento</h1>
    </div>

    <!-- Estadísticas -->
    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-icon text-primary">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total</div>
                <div class="stat-value" id="statTotal">-</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon text-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Pendientes</div>
                <div class="stat-value" id="statPendientes">-</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Aprobados</div>
                <div class="stat-value" id="statAprobados">-</div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="action-bar">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importarModal">
            <i class="fas fa-file-upload"></i> Importar Formulario
        </button>
        <button type="button" class="btn btn-success" id="exportBtn">
            <i class="fas fa-file-excel"></i> Exportar a Excel
        </button>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
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
                    <option value="">Todas</option>
                </select>
            </div>
            <div class="filter-item">
                <label><i class="fas fa-user-cog"></i> Técnico</label>
                <select class="form-control" id="filterTecnico">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="filter-item">
                <label><i class="fas fa-toggle-on"></i> Estado</label>
                <select class="form-control" id="filterEstado">
                    <option value="">Todos</option>
                    <option value="0">Pendientes</option>
                    <option value="1">Aprobados</option>
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

<!-- Modal para importar CSV -->
<div class="modal fade" id="importarModal" tabindex="-1" aria-labelledby="importarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="importarModalLabel">
                    <i class="fas fa-file-upload"></i> Importar Formulario desde CSV
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importarCSVForm">
                    <div class="form-group">
                        <label for="csv_data">Pega aquí el contenido compartido por el técnico:</label>
                        <textarea class="form-control" name="csv_data" id="csv_data" rows="10" placeholder="Pega aquí el código compartido por el técnico"></textarea>
                    </div>
                    <input type="hidden" name="confirm_insert" value="1">
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-arrow-up"></i> Importar Formulario
                    </button>
                </form>
                <div id="importResult" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let table;

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    initializeDataTable();
    loadFilterOptions();
    loadStats();
    setupImportForm();
    setupExportButton();
});

// Inicializar DataTable
function initializeDataTable() {
    table = $('#formularios').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get_formularios.php',
            type: 'POST',
            data: function(d) {
                // Agregar filtros personalizados
                d.filterFechaInicio = $('#filterFechaInicio').val();
                d.filterFechaFin = $('#filterFechaFin').val();
                d.filterTienda = $('#filterTienda').val();
                d.filterTecnico = $('#filterTecnico').val();
                d.filterEstado = $('#filterEstado').val();
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
                    var estadoClass = (data == 1) ? 'estado-aprobado' : 'estado-pendiente';
                    var estadoTexto = (data == 1) ? 'Aprobado' : 'Pendiente';
                    return '<span class="' + estadoClass + '">' + estadoTexto + '</span>';
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100, 200],
        createdRow: function(row, data, dataIndex) {
            var params = { id: data.id };
            var fi = $('#filterFechaInicio').val();
            var ff = $('#filterFechaFin').val();
            var ft = $('#filterTienda').val();
            var ftec = $('#filterTecnico').val();
            var fe = $('#filterEstado').val();
            var fs = (typeof table !== 'undefined' && table) ? table.search() : '';
            if (fi) params.filterFechaInicio = fi;
            if (ff) params.filterFechaFin = ff;
            if (ft) params.filterTienda = ft;
            if (ftec) params.filterTecnico = ftec;
            if (fe !== '') params.filterEstado = fe;
            if (fs) params.search = fs;
            $(row).attr('data-href', '/admin/form/?' + $.param(params));
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
        url: 'get_filter_options.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Cargar tiendas
            if(data.tiendas) {
                $('#filterTienda').empty().append('<option value="">Todas</option>');
                data.tiendas.forEach(function(tienda) {
                    $('#filterTienda').append('<option value="' + tienda + '">' + tienda + '</option>');
                });
            }

            // Cargar técnicos
            if(data.tecnicos) {
                $('#filterTecnico').empty().append('<option value="">Todos</option>');
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
    const filterData = {
        filterFechaInicio: $('#filterFechaInicio').val(),
        filterFechaFin: $('#filterFechaFin').val(),
        filterTienda: $('#filterTienda').val(),
        filterTecnico: $('#filterTecnico').val(),
        filterEstado: $('#filterEstado').val()
    };

    $.ajax({
        url: 'get_stats.php',
        type: 'POST',
        data: filterData,
        dataType: 'json',
        success: function(data) {
            $('#statTotal').text(data.total || 0);
            $('#statPendientes').text(data.pendientes || 0);
            $('#statAprobados').text(data.aprobados || 0);
        },
        error: function() {
            console.error('Error cargando estadísticas');
        }
    });
}

// Aplicar filtros
function applyFilters() {
    table.ajax.reload();
}

// Limpiar filtros
function clearFilters() {
    $('#filterFechaInicio').val('');
    $('#filterFechaFin').val('');
    $('#filterTienda').val('');
    $('#filterTecnico').val('');
    $('#filterEstado').val('');
    table.ajax.reload();
}

// Setup exportar a Excel
function setupExportButton() {
    $('#exportBtn').on('click', function() {
        const params = {
            filterFechaInicio: $('#filterFechaInicio').val(),
            filterFechaFin: $('#filterFechaFin').val(),
            filterTienda: $('#filterTienda').val(),
            filterTecnico: $('#filterTecnico').val(),
            filterEstado: $('#filterEstado').val()
        };
        
        window.location.href = 'export_excel.php?' + $.param(params);
    });
}

// Setup formulario de importación
function setupImportForm() {
    $("#importarCSVForm").submit(function(event) {
        event.preventDefault();

        var csvData = $("#csv_data").val().trim();

        if (!csvData) {
            $("#importResult").html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Por favor, pega el contenido del CSV.</div>');
            return;
        }

        $.ajax({
            url: "insert_csv.php",
            type: "POST",
            data: { csv_data: csvData, confirm_insert: 1 },
            beforeSend: function() {
                $("#importResult").html('<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Procesando CSV...</div>');
            },
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    if (result.success) {
                        $("#importResult").html('<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + result.message + '</div>');
                        setTimeout(function() { 
                            table.ajax.reload();
                            $('#importarModal').modal('hide');
                            $('#csv_data').val('');
                            $("#importResult").empty();
                        }, 2000);
                    } else {
                        $("#importResult").html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + result.message + '</div>');
                    }
                } catch (e) {
                    $("#importResult").html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error inesperado.</div>');
                }
            },
            error: function() {
                $("#importResult").html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error al enviar los datos.</div>');
            }
        });
    });
}
</script>

<?php include('../inc/menu-foot.php');?>
<?php include('../inc/footer.php');?>
</body>
</html>

<?php
$conexion->close();
?>

