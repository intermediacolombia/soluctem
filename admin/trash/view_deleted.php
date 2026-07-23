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

$formulario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($formulario_id > 0) {
    // Solo mostrar si está borrado = 1
    $sql_formulario = "SELECT * FROM formulario WHERE id = $formulario_id AND borrado = 1";
    $resultado_formulario = $conexion->query($sql_formulario);

    if ($resultado_formulario->num_rows > 0) {
        $row = $resultado_formulario->fetch_assoc();
        
        // Obtener imágenes
        $sql_imagenes = "SELECT imagen FROM imagenes WHERE formulario_id = $formulario_id";
        $resultado_imagenes = $conexion->query($sql_imagenes);
    } else {
        die("Formulario no encontrado en la papelera.");
    }
} else {
    die("ID de formulario no válido.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include('../inc/header.php'); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Eliminado - Vista Previa</title>
    <link rel="stylesheet" href="../css/style.css?<?php echo time(); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="../css/lightbox.min.css" rel="stylesheet"/>

    <style>
        /* Estilo deshabilitado/bloqueado */
        body {
            background-color: #f5f5f5;
        }

        .deleted-banner {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .deleted-banner h1 {
            margin: 0;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .deleted-banner .subtitle {
            margin-top: 8px;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Tabla deshabilitada */
        table {
            opacity: 0.7;
            background-color: #e9ecef !important;
            pointer-events: none;
            user-select: none;
        }

        table th {
            background-color: #dee2e6 !important;
            color: #6c757d !important;
        }

        table td {
            background-color: #f8f9fa !important;
            color: #6c757d !important;
        }

        table .section-header {
            background-color: #ced4da !important;
            color: #495057 !important;
        }

        /* Imágenes deshabilitadas */
        .image-container {
            opacity: 0.6;
            pointer-events: none;
            filter: grayscale(50%);
        }

        .image-container img {
            cursor: not-allowed;
        }

        /* Botones de acción */
        .btn-group {
            margin-bottom: 20px;
        }

        .btn {
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 1rem;
            margin: 10px 5px;
            padding: 12px 24px;
            font-weight: 600;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #495057);
            border: none;
            color: white;
        }

        .btn-secondary:hover {
            background: linear-gradient(45deg, #495057, #343a40);
        }

        .btn-success {
            background: linear-gradient(45deg, #28a745, #1e7e34);
            border: none;
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(45deg, #1e7e34, #155724);
        }

        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #a71d2a);
            border: none;
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(45deg, #a71d2a, #8b1a22);
        }

        /* Alerta de advertencia */
        .danger-alert {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: 3px solid #dc3545;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .danger-alert h4 {
            color: #721c24;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .danger-alert p {
            color: #721c24;
            margin: 0;
            font-size: 1rem;
        }

        .danger-alert .icon {
            font-size: 3rem;
            color: #dc3545;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Info boxes */
        .info-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-box i {
            color: #856404;
            margin-right: 10px;
        }

        .info-box p {
            color: #856404;
            margin: 0;
            font-weight: 600;
        }

        /* Modal styling */
        .modal-header.bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
        }

        .modal-body .warning-icon {
            font-size: 5rem;
            color: #dc3545;
            animation: shake 0.5s infinite;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        h1, h2 {
            color: #495057;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<?php include('../inc/menu.php'); ?>

<div class="container mt-4">
    <!-- Banner de eliminado -->
    <div class="deleted-banner">
        <h1>
            <i class="fas fa-trash-restore-alt"></i>
            Formulario Eliminado - Vista Previa
        </h1>
        <div class="subtitle">
            Este formulario está en la papelera. Todos los controles están deshabilitados.
        </div>
    </div>

    <!-- Info de retención -->
    <div class="info-box d-flex align-items-center">
        <i class="fas fa-info-circle fa-2x"></i>
        <p>Este formulario se eliminará automáticamente después de 3 meses en la papelera. Fue eliminado el: <strong><?php echo date('d/m/Y H:i', strtotime($row['fecha_eliminacion'] ?? $row['fecha'])); ?></strong></p>
    </div>

    <!-- Mensajes -->
    <?php if (isset($_SESSION['permanent_delete_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['permanent_delete_success']); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php unset($_SESSION['permanent_delete_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['permanent_delete_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['permanent_delete_error']); ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php unset($_SESSION['permanent_delete_error']); ?>
    <?php endif; ?>

    <h1>Formulario de Mantenimiento Nro. <?php echo $formulario_id; ?></h1>

    <!-- Botones de acción -->
    <div class="btn-group" role="group">
        <a href="index.php" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left"></i> Volver a Papelera
        </a>
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#restoreModal">
            <i class="fas fa-trash-restore"></i> Recuperar Formulario
        </button>
        <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#permanentDeleteModal">
            <i class="fas fa-exclamation-triangle"></i> Borrar Definitivamente
        </button>
    </div>

    <!-- Alerta de peligro -->
    <div class="danger-alert d-flex align-items-center">
        <div class="icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="flex-grow-1">
            <h4><i class="fas fa-skull-crossbones"></i> ADVERTENCIA: Eliminación Permanente</h4>
            <p>Si elimina este formulario definitivamente, <strong>NO PODRÁ RECUPERARLO JAMÁS</strong>. Esta acción es irreversible y el formulario será borrado permanentemente de la base de datos.</p>
        </div>
    </div>

    <!-- Contenido del formulario (deshabilitado) -->
    <table>
        <tr class="section-header">
            <td colspan="4"><center>SOLICITANTE Y TIENDA BENEFICIARIA</center></td>
        </tr>
        <tr>
            <th>Nombre del Solicitante</th>
            <td><?php echo htmlspecialchars($row['nombreSolicitante']); ?></td>
            <th>Cargo</th>
            <td><?php echo htmlspecialchars($row['cargo']); ?></td>
        </tr>
        <tr>
            <th>Nombre de la Tienda</th>
            <td><?php echo htmlspecialchars($row['nombreTienda']); ?></td>
            <th>Número de Tienda</th>
            <td><?php echo htmlspecialchars($row['numeroTienda']); ?></td>
        </tr>
        <tr>
            <th>Número de Ticket</th>
            <td><?php echo htmlspecialchars($row['numeroTicket']); ?></td>
            <th>Fecha</th>
            <td><?php echo htmlspecialchars($row['fecha']); ?></td>
        </tr>
        <tr>
            <th>Departamento</th>
            <td><?php echo htmlspecialchars($row['departamento']); ?></td>
            <th>Municipio</th>
            <td><?php echo htmlspecialchars($row['municipio']); ?></td>
        </tr>

        <!-- Resto de secciones igual que el formulario original -->
        <tr class="section-header">
            <td colspan="4"><center>TIPO DE ASISTENCIA</center></td>
        </tr>
        <tr>
            <td>Reparación</td>
            <td><?php echo ($row['asistenciaReparacion'] == 1) ? 'X' : ''; ?></td>
            <td>Garantía</td>
            <td><?php echo ($row['asistenciaGarantia'] == 1) ? 'X' : ''; ?></td>
        </tr>
        <tr>
            <td>Ajuste</td>
            <td><?php echo ($row['asistenciaAjuste'] == 1) ? 'X' : ''; ?></td>
            <td>Modificación</td>
            <td><?php echo ($row['asistenciaModificacion'] == 1) ? 'X' : ''; ?></td>
        </tr>
        <tr>
            <td>Servicio</td>
            <td><?php echo ($row['asistenciaServicio'] == 1) ? 'X' : ''; ?></td>
            <td>Mejora</td>
            <td><?php echo ($row['asistenciaMejora'] == 1) ? 'X' : ''; ?></td>
        </tr>

        <!-- INFORMACIÓN ÁREA TÉCNICA -->
        <tr class="section-header">
            <td colspan="4"><center>INFORMACIÓN ÁREA TÉCNICA</center></td>
        </tr>
        <tr>
            <th>Nombre del Equipo</th>
            <td><?php echo htmlspecialchars($row['nombreEquipo']); ?></td>
            <th>Marca</th>
            <td><?php echo htmlspecialchars($row['marca']); ?></td>
        </tr>
        <tr>
            <th>Serial</th>
            <td colspan="3"><?php echo htmlspecialchars($row['serial']); ?></td>
        </tr>
        <tr>
            <th>Descripción de la Falla</th>
            <td colspan="3"><?php echo htmlspecialchars($row['descripcionFalla']); ?></td>
        </tr>
        <tr>
            <th>Diagnóstico Técnico</th>
            <td colspan="3"><?php echo htmlspecialchars($row['diagnosticoTecnico']); ?></td>
        </tr>
        <tr>
            <th>Repuestos Cambiados</th>
            <td colspan="3"><?php echo htmlspecialchars($row['repuestosCambiados']); ?></td>
        </tr>
        <tr>
            <th>Observaciones</th>
            <td colspan="3"><?php echo htmlspecialchars($row['observaciones']); ?></td>
        </tr>

        <!-- CONTRATISTAS -->
        <tr class="section-header">
            <td colspan="4"><center>CONSTANCIA DE REALIZACIÓN DE ASISTENCIA</center></td>
        </tr>
        <tr>
            <th>Contratista</th>
            <th>Cédula</th>
            <th>Hora Entrada</th>
            <th>Hora Salida</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($row['contratista1'] ?: ''); ?></td>
            <td><?php echo htmlspecialchars($row['cedula1'] ?: ''); ?></td>
            <td><?php echo htmlspecialchars($row['horaEntrada1'] ?: ''); ?></td>
            <td><?php echo htmlspecialchars($row['horaSalida1'] ?: ''); ?></td>
        </tr>

        <!-- FUNCIONARIO -->
        <tr class="section-header">
            <td colspan="4"><center>FUNCIONARIO</center></td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td><?php echo htmlspecialchars($row['nombreFuncionario']); ?></td>
            <th>Cédula</th>
            <td><?php echo htmlspecialchars($row['cedulaFuncionario']); ?></td>
        </tr>

        <!-- FIRMAS -->
        <tr class="section-header">
            <td colspan="4"><center>FIRMAS</center></td>
        </tr>
        <tr>
            <th>Firma del Cliente</th>
            <td>
                <?php if (!empty($row['firma_digital_cliente'])): ?>
                    <img src="<?php echo htmlspecialchars($row['firma_digital_cliente']); ?>" alt="Firma del Cliente" width="200" style="opacity: 0.5;">
                <?php else: ?>
                    No disponible
                <?php endif; ?>
            </td>
            <th>Firma del Técnico</th>
            <td>
                <?php if (!empty($row['firma_digital_tecnico'])): ?>
                    <img src="<?php echo htmlspecialchars($row['firma_digital_tecnico']); ?>" alt="Firma del Técnico" width="200" style="opacity: 0.5;">
                <?php else: ?>
                    No disponible
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <h2 style="color: #6c757d;">Imágenes Asociadas (Deshabilitadas)</h2>
    <div class="image-container">
        <?php if ($resultado_imagenes->num_rows > 0): ?>
            <?php while ($img_row = $resultado_imagenes->fetch_assoc()): ?>
                <img src="<?php echo $url_sys . htmlspecialchars($img_row['imagen']); ?>" alt="Imagen" style="max-width: 150px; margin: 5px;">
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: #6c757d;">No hay imágenes asociadas.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Recuperar Formulario -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="restore_form.php" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-trash-restore"></i> Recuperar Formulario
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-trash-restore-alt" style="font-size: 5rem; color: #28a745;"></i>
                    </div>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($formulario_id); ?>">
                    <h5 class="mb-3">¿Desea recuperar este formulario?</h5>
                    <div class="alert alert-info text-left">
                        <p class="mb-2"><strong>Al recuperar el formulario:</strong></p>
                        <ul class="text-left mb-0">
                            <li>Volverá a estar disponible en el sistema</li>
                            <li>Se podrá editar y gestionar normalmente</li>
                            <li>Mantendrá todos sus datos e imágenes</li>
                            <li>Saldrá de la papelera</li>
                        </ul>
                    </div>
                    <p class="text-muted">Formulario: <strong>#<?php echo htmlspecialchars($formulario_id); ?></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Sí, Recuperar Formulario
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación Permanente -->
<div class="modal fade" id="permanentDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="delete_permanent.php" method="POST">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> ELIMINAR DEFINITIVAMENTE
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="warning-icon mb-3">
                        <i class="fas fa-skull-crossbones"></i>
                    </div>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($formulario_id); ?>">
                    <h4 class="text-danger font-weight-bold mb-3">⚠️ ADVERTENCIA CRÍTICA ⚠️</h4>
                    <div class="alert alert-danger text-left">
                        <p class="font-weight-bold mb-2">Está a punto de eliminar PERMANENTEMENTE el formulario <strong>#<?php echo htmlspecialchars($formulario_id); ?></strong></p>
                        <ul class="text-left">
                            <li>Esta acción es <strong>IRREVERSIBLE</strong></li>
                            <li>El formulario será <strong>BORRADO DE LA BASE DE DATOS</strong></li>
                            <li><strong>NO PODRÁ RECUPERARLO NUNCA</strong></li>
                            <li>Todas las imágenes y datos asociados serán eliminados</li>
                        </ul>
                    </div>
                    <p class="font-weight-bold text-danger">¿Está absolutamente seguro de continuar?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> SÍ, ELIMINAR DEFINITIVAMENTE
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('../inc/menu-foot.php'); ?>
<?php include('../inc/footer.php'); ?>
</body>
</html>

<?php
$conexion->close();
?>
