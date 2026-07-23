<?php 
include('../login/sesion.php');

// Verificar que el usuario sea administrador
if ($_SESSION['rol'] !== 'Administrador') {
    header('Location: ../index.php');
    exit();
}

include('../inc/config.php');

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelera de Formularios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <?php include('../inc/header.php');?>
    <style>
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header de papelera */
        .page-header {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
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

        /* Alerta de retención */
        .retention-alert {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
        }

        .retention-alert i {
            font-size: 2rem;
            color: #856404;
            margin-right: 15px;
        }

        .retention-alert .content {
            flex: 1;
        }

        .retention-alert h5 {
            color: #856404;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .retention-alert p {
            color: #856404;
            margin: 0;
        }

        /* Estadísticas */
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
            border-left: 5px solid #6c757d;
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.7;
            color: #6c757d;
        }

        .stat-content .label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }

        .stat-content .value {
            font-size: 2.2rem;
            font-weight: 700;
            color: #212529;
        }

        /* Filtros */
        .filters-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
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
        }

        /* Tabla */
        table tbody tr {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .estado-borrado {
            background-color: #e9ecef;
            color: #495057;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        table th, table td {
            padding: 8px;
            font-size: 14px;
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

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
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
        }
    </style>
</head>
<body>
<?php include('../inc/menu.php')?>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-border text-secondary" role="status" style="width: 3rem; height: 3rem;">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3 text-muted">Cargando papelera...</p>
    </div>
</div>

<!-- Mensajes -->
<div class="container mt-4">
    <?php if (isset($_SESSION['restore_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['restore_success']); ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['restore_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['restore_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['restore_error']); ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['restore_error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['delete_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['delete_success']); ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['delete_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['delete_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['delete_error']); ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['delete_error']); ?>
    <?php endif; ?>
</div>

<div class="container mt-4">
    <!-- Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-trash-alt"></i>
            Papelera de Formularios
        </h1>
        <div class="subtitle">
            Formularios eliminados temporalmente - Solo visible para administradores
        </div>
    </div>

    <!-- Alerta de Retención -->
    <div class="retention-alert d-flex align-items-center">
        <i class="fas fa-info-circle"></i>
        <div class="content">
            <h5><i class="fas fa-clock"></i> Política de Retención</h5>
            <p>Los formularios en la papelera se conservarán durante <strong>3 meses</strong> desde su fecha de eliminación. Después de este periodo, serán eliminados automáticamente y de forma permanente del sistema.</p>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-trash-restore"></i>
                </div>
                <div class="stat-content">
                    <div class="label">Total en Papelera</div>
                    <div class="value" id="statTotal">-</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <div class="label">Eliminados Hoy</div>
                    <div class="value" id="statHoy">-</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-content">
                    <div class="label">Esta Semana</div>
                    <div class="value" id="statSemana">-</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <div class="filters-title">
            <i class="fas fa-filter"></i> Filtros
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
                <button type="button" class="btn btn-secondary btn-filter" onclick="applyFilters()">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <button type="button" class="btn btn-outline-secondary btn-filter" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla -->
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
                    <th>Fecha Eliminación</th>
                    <th>Usuario Eliminó</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <!-- Datos cargados vía AJAX -->
            </tbody>
        </table>
    </div>
</div>

<script>
let table;

$(document).ready(function() {
    initializeDataTable();
    loadFilterOptions();
    loadStats();
});

function initializeDataTable() {
    table = $('#formularios').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get_papelera.php',
            type: 'POST',
            data: function(d) {
                d.filterFechaInicio = $('#filterFechaInicio').val();
                d.filterFechaFin = $('#filterFechaFin').val();
                d.filterTienda = $('#filterTienda').val();
                d.filterTecnico = $('#filterTecnico').val();
            },
            beforeSend: function() {
                $('#loadingOverlay').addClass('active');
            },
            complete: function() {
                $('#loadingOverlay').removeClass('active');
            },
            error: function(xhr, error, thrown) {
                console.error('Error:', error);
                alert('Error al cargar los datos.');
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
                data: 'fecha_eliminacion',
                render: function(data, type, row) {
                    if (!data) return '<span class="text-muted">-</span>';
                    const fecha = new Date(data);
                    return fecha.toLocaleString('es-CO', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            },
            { 
                data: 'usuario_eliminacion',
                render: function(data, type, row) {
                    return data ? '<span class="badge badge-secondary">' + data + '</span>' : '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'estado',
                render: function(data, type, row) {
                    return '<span class="estado-borrado"><i class="fas fa-trash-alt"></i> Eliminado</span>';
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
            $(row).attr('data-href', 'view_deleted.php?id=' + data.id);
            $(row).css('cursor', 'pointer');
        },
        drawCallback: function(settings) {
            loadStats();
        }
    });

    $('#formularios tbody').on('click', 'tr', function() {
        var href = $(this).data('href');
        if(href) {
            window.location.href = href;
        }
    });
}

function loadFilterOptions() {
    $.ajax({
        url: 'get_filter_options_papelera.php',
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
        }
    });
}

function loadStats() {
    $.ajax({
        url: 'get_stats_papelera.php',
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
        }
    });
}

function applyFilters() {
    table.ajax.reload();
}

function clearFilters() {
    $('#filterFechaInicio').val('');
    $('#filterFechaFin').val('');
    $('#filterTienda').val('');
    $('#filterTecnico').val('');
    table.ajax.reload();
}
</script>

<?php include('../inc/menu-foot.php');?>
<?php include('../inc/footer.php');?>
</body>
</html>

<?php
$conexion->close();
?>
