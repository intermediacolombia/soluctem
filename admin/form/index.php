<?php include('../login/sesion.php'); ?>

<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<?php
// Conexión a la base de datos
include('../inc/config.php');

$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener el id del formulario desde la URL
$formulario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$navigation_params = array();
$navigation_query = "";
$next_formulario_id = null;

if ($formulario_id > 0) {
    // Consulta SQL para obtener los datos del formulario
    $sql_formulario = "SELECT * FROM formulario WHERE id = $formulario_id AND borrado = 0";
    $resultado_formulario = $conexion->query($sql_formulario);

    if ($resultado_formulario->num_rows > 0) {
        // Obtener los datos del formulario
        $row = $resultado_formulario->fetch_assoc();

        // Obtener el departamento del formulario
        $departamento_formulario = $row['departamento'];

        // Obtener las zonas del usuario desde la sesión
        $zonas_usuario = $_SESSION['zonas']; // Esto es una cadena con las zonas separadas por comas

        // Convertir las zonas del usuario en un array
        $zonas_array = explode(',', $zonas_usuario);
        $zonas_array = array_map('trim', $zonas_array); // Eliminar espacios en blanco

        // Verificar si el usuario tiene permiso para ver este formulario
        if (in_array($departamento_formulario, $zonas_array)) {
            // El usuario tiene permiso, continuar
            // Consulta SQL para obtener las imágenes asociadas al formulario
            $sql_imagenes = "SELECT imagen FROM imagenes WHERE formulario_id = $formulario_id";
            $resultado_imagenes = $conexion->query($sql_imagenes);
            $tiene_permiso = true;

            $allowed_navigation_params = array(
                'filterFechaInicio',
                'filterFechaFin',
                'filterTienda',
                'filterTecnico',
                'filterEstado',
                'search'
            );

            foreach ($allowed_navigation_params as $param) {
                if (isset($_GET[$param]) && $_GET[$param] !== '') {
                    $navigation_params[$param] = $_GET[$param];
                }
            }
            $navigation_query = http_build_query($navigation_params);

            $zonas_escapadas = array();
            foreach ($zonas_array as $zona) {
                $zonas_escapadas[] = "'" . $conexion->real_escape_string($zona) . "'";
            }
            $zonas_para_sql = implode(',', $zonas_escapadas);
            $whereBase = "departamento IN ($zonas_para_sql) AND borrado = 0 AND (nombreSolicitante IS NOT NULL AND nombreSolicitante != '')";
            $customFilters = array();

            if (!empty($_GET['filterFechaInicio'])) {
                $fechaInicio = $conexion->real_escape_string($_GET['filterFechaInicio']);
                $customFilters[] = "fecha >= '$fechaInicio'";
            }

            if (!empty($_GET['filterFechaFin'])) {
                $fechaFin = $conexion->real_escape_string($_GET['filterFechaFin']);
                $customFilters[] = "fecha <= '$fechaFin'";
            }

            if (!empty($_GET['filterTienda'])) {
                $tienda = $conexion->real_escape_string($_GET['filterTienda']);
                $customFilters[] = "nombreTienda = '$tienda'";
            }

            if (!empty($_GET['filterTecnico'])) {
                $tecnico = $conexion->real_escape_string($_GET['filterTecnico']);
                $customFilters[] = "nombreSolicitante = '$tecnico'";
            }

            if (isset($_GET['filterEstado']) && $_GET['filterEstado'] !== '') {
                $estado = intval($_GET['filterEstado']);
                $customFilters[] = "estado = $estado";
            }

            $whereCustom = !empty($customFilters) ? " AND " . implode(' AND ', $customFilters) : "";
            $whereSearch = "";

            if (!empty($_GET['search'])) {
                $search = $conexion->real_escape_string($_GET['search']);
                $whereSearch = " AND (
                    id LIKE '%$search%' OR
                    nombreSolicitante LIKE '%$search%' OR
                    nombreTienda LIKE '%$search%' OR
                    numeroTicket LIKE '%$search%' OR
                    fecha LIKE '%$search%' OR
                    departamento LIKE '%$search%'
                )";
            }

            $whereClause = $whereBase . $whereCustom . $whereSearch;
            $next_formulario_id = null;
            $sql_next = "SELECT id FROM formulario WHERE $whereClause AND id < $formulario_id ORDER BY id DESC LIMIT 1";
            $resultado_next = $conexion->query($sql_next);

            if ($resultado_next && $resultado_next->num_rows > 0) {
                $next_formulario_id = intval($resultado_next->fetch_assoc()['id']);
            }
        } else {
            // El usuario no tiene permiso
            $tiene_permiso = false;
        }
    } else {
        die("No se encontró el formulario.");
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
    <title>Formulario para Imprimir</title>
    <link rel="stylesheet" href="../css/style.css?<?php echo time(); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- Incluir FontAwesome para iconos -->
	<!-- Incluir FontAwesome para iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
	
	 <!-- Incluir Bootstrap CSS para estilos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Incluir FontAwesome para iconos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	





<!-- Estilos personalizados -->
    <style>
        /* Estilos personalizados para botones */
		.btn-group{
			margin-bottom: 15px;
			
		}
		.btn-group a:hover{
			text-decoration: none;
			
		}
        .btn {
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 1rem;
            margin: 15px;
			padding: 10px;
			text-decoration: none;
        }

        .btn-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-group .btn i {
            margin-right: 5px;
        }

        .btn-group .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }

        /* Botones con Gradiente */
        .btn-outline-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(45deg, #0056b3, #004080);
            color: white;
            border: none;
        }

        .btn-outline-danger {
            background: linear-gradient(45deg, #dc3545, #a71d2a);
            color: white;
            border: none;
        }

        .btn-outline-danger:hover {
            background: linear-gradient(45deg, #a71d2a, #8b1a22);
            color: white;
            border: none;
        }

        .btn-outline-success {
            background: linear-gradient(45deg, #28a745, #1e7e34);
            color: white;
            border: none;
        }

        .btn-outline-success:hover {
            background: linear-gradient(45deg, #1e7e34, #155724);
            color: white;
            border: none;
        }

        .btn-success {
            background: linear-gradient(45deg, #28a745, #1e7e34);
            color: white;
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(45deg, #1e7e34, #155724);
            color: white;
            border: none;
        }
		
		.image-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.image-container img {
    max-width: 100%;
    height: auto;
}
    </style>
  <link href="../css/lightbox.min.css" rel="stylesheet"/>

</head>
<body>
<?php include('../inc/menu.php'); ?>

	<?php
// Mostrar mensajes de actualización
session_start();
if (isset($_SESSION['update_success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['update_success']) . '
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>';
    unset($_SESSION['update_success']);
}

if (isset($_SESSION['update_error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['update_error']) . '
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>';
    unset($_SESSION['update_error']);
}
?>

	
<?php
if (isset($tiene_permiso) && !$tiene_permiso) {
    // Mostrar el mensaje después del menú
    echo "<div class='alert alert-danger text-center'>No tiene permisos para ver este formulario.</div>";
} else {
    // Mostrar el contenido del formulario
    ?>
    <h1>Formulario de Mantenimiento Nro. <?php echo $formulario_id; ?></h1>
    <!-- Mostrar los botones de editar, generar PDF, etc. -->
<div class="container mt-5">
	<?php
session_start(); // Debes iniciar la sesión antes de usar $_SESSION
?>
<?php
// Mostrar mensajes de aprobación
if (isset($_SESSION['approve_success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['approve_success']) . '
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>';
    unset($_SESSION['approve_success']);
}

if (isset($_SESSION['approve_error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['approve_error']) . '
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>';
    unset($_SESSION['approve_error']);
}
?>
	
        <!-- Mostrar los botones de editar, generar PDF, etc. -->
        <div class="btn-group" role="group" aria-label="Acciones">
            <?php if ($row['estado'] == 0): ?>
                <a href="edit.php?id=<?php echo $formulario_id; ?><?php echo $navigation_query ? '&' . htmlspecialchars($navigation_query) : ''; ?>" class="btn btn-outline-primary btn-lg m-1" data-toggle="tooltip" data-placement="top" title="Editar Usuario">
                    <i class="fa fa-edit fa-lg"></i> Editar
                </a>
            <?php endif; ?>

            <!--<a href="<?php echo $url_sys;?>/admin/pdf/?id=<?php echo $formulario_id;?>" class="btn btn-outline-danger btn-lg m-1" data-toggle="tooltip" data-placement="top" title="Generar PDF">
                <i class="fa fa-file-pdf fa-lg"></i> Generar PDF
            </a>-->
			<a href="#" onclick="generarPDF('<?php echo $url_sys; ?>/admin/pdf/?id=<?php echo $formulario_id; ?>', '<?php echo $formulario_id; ?>'); return false;" class="btn btn-outline-danger btn-lg m-1" data-toggle="tooltip" data-placement="top" title="Generar PDF">
    <i class="fa fa-file-pdf fa-lg"></i> Generar PDF
</a>

            <?php if (!empty($next_formulario_id)): ?>
                <?php $next_url = '?' . http_build_query(array_merge(array('id' => $next_formulario_id), $navigation_params)); ?>
                <a href="<?php echo htmlspecialchars($next_url); ?>" class="btn btn-outline-primary btn-lg m-1" data-toggle="tooltip" data-placement="top" title="Ir al siguiente formulario filtrado">
                    <i class="fa fa-arrow-right fa-lg"></i> Siguiente filtrado
                </a>
            <?php else: ?>
                <button type="button" class="btn btn-outline-primary btn-lg m-1" disabled data-toggle="tooltip" data-placement="top" title="No hay m&aacute;s formularios con estos filtros">
                    <i class="fa fa-arrow-right fa-lg"></i> Siguiente filtrado
                </button>
            <?php endif; ?>


			
			




            <!--<a href="<?php echo $url_sys;?>/admin/excel/?id=<?php echo $formulario_id;?>" class="btn btn-outline-success btn-lg m-1" target="_blank" data-toggle="tooltip" data-placement="top" title="Generar Excel">
                <i class="fa fa-file-excel fa-lg"></i> Generar Excel
            </a>-->
			
			<?php if ($_SESSION['rol'] === 'Administrador' || $_SESSION['rol'] === 'Usuario'): ?>
			<?php if ($row['estado'] == 0): ?>
    <a href="#" class="btn btn-outline-danger btn-lg m-1" data-toggle="modal" data-target="#deleteModal" data-toggle="tooltip" data-placement="top" title="Eliminar Formulario">
        <i class="fas fa-trash fa-lg"></i> Borrar
    </a>
<?php endif; ?>
			<?php endif; ?>



            <?php if ($row['estado'] == 0): ?>
                <a href="" class="btn btn-success btn-lg m-1" data-toggle="modal" data-target="#aproveModal" data-toggle="tooltip" data-placement="top" title="Aprobar Formulario">
                    <i class="fa fa-check-circle fa-lg"></i> Aprobar
                </a>
            <?php else: ?>
                <button type="button" class="btn btn-success btn-lg m-1" disabled data-toggle="tooltip" data-placement="top" title="Formulario Aprobado">
                    <i class="fas fa-check-circle fa-lg"></i> Aprobado
                </button>
            <?php endif; ?>
        </div>
    </div>



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

        <!-- Sección de Tipo de Asistencia -->
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
        <tr>
            <td>Combinación</td>
            <td><?php echo ($row['asistenciaCombinacion'] == 1) ? 'X' : ''; ?></td>
            <td colspan="2"></td>
        </tr>

        <!-- Sección de Tipo/Causa de Fallas Básicas -->
        <tr class="section-header">
            <td colspan="4"><center>TIPO/CAUSA DE FALLAS BÁSICAS</center></td>
        </tr>
        <tr>
            <td>Operación</td>
            <td><?php echo ($row['fallaOperacion'] == 1) ? 'X' : ''; ?></td>
            <td>Mecánica</td>
            <td><?php echo ($row['fallaMecanica'] == 1) ? 'X' : ''; ?></td>
        </tr>
        <tr>
            <td>Eléctrica</td>
            <td><?php echo ($row['fallaElectrica'] == 1) ? 'X' : ''; ?></td>
            <td>Terceros</td>
            <td><?php echo ($row['fallaTerceros'] == 1) ? 'X' : ''; ?></td>
        </tr>
        <tr>
            <td>Fabricación</td>
            <td><?php echo ($row['fallaFabricacion'] == 1) ? 'X' : ''; ?></td>
            <td colspan="2"></td>
        </tr>

        <!-- Información Área Técnica -->
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

        <!-- Evaluación del Servicio -->
        <tr class="section-header">
            <td colspan="4"><center>EVALUACIÓN DEL SERVICIO</center></td>
        </tr>
        <tr>
            <th colspan="3">Descripción</th>
            <th>Cumple/No Cumple</th>
        </tr>
        <tr class="section-header">
            <td colspan="4"><center>SEGURIDAD</center></td>
        </tr>
        <tr>
            <td colspan="3">La labor realizada genera un alto riesgo de accidentalidad para los clientes y/o colaboradores</td>
            <td><?php echo htmlspecialchars($row['seguridadRiesgoAccidentalidad']); ?></td>
        </tr>
        <tr>
            <td colspan="3">La labor realizada ofrece algún riesgo para la integridad del equipo</td>
            <td><?php echo htmlspecialchars($row['seguridadRiesgoEquipo']); ?></td>
        </tr>
        <tr class="section-header">
            <td colspan="4"><center>FUNCIONALIDAD</center></td>
        </tr>
        <tr>
            <td colspan="3">La falla reportada fue solucionada con el trabajo realizado</td>
            <td><?php echo htmlspecialchars($row['funcionamientoFallaSolucionada']); ?></td>
        </tr>
        <tr>
            <td colspan="3">Los pasos normales de manejo se siguen sin procedimientos extra</td>
            <td><?php echo htmlspecialchars($row['funcionamientoPasosNormales']); ?></td>
        </tr>
        <tr class="section-header">
            <td colspan="4"><center>CALIDAD</center></td>
        </tr>
        <tr>
            <td colspan="3">La calidad del trabajo fue adecuada</td>
            <td><?php echo htmlspecialchars($row['calidadTrabajo']); ?></td>
        </tr>
        <tr class="section-header">
            <td colspan="4"><center>LIMPIEZA</center></td>
        </tr>
        <tr>
            <td colspan="3">Limpieza - El área intervenida fue dejada organizada y aseada</td>
            <td><?php echo htmlspecialchars($row['limpiezaOrganizacionArmado']); ?></td>
        </tr>
        <tr>
            <td colspan="3">Limpieza - Los escombros fueron limpiados</td>
            <td><?php echo htmlspecialchars($row['limpiezaOrganizacionAseado']); ?></td>
        </tr>
        <!-- CAPACITACIÓN -->
        <tr class="section-header">
            <td colspan="4"><center>CAPACITACIÓN</center></td>
        </tr>
        <tr>
            <td colspan="3">Se indicó la causa de la novedad al personal que recibió el trabajo</td>
            <td><?php echo htmlspecialchars($row['capacitacionCausa']); ?></td>
        </tr>
        <tr>
            <td colspan="3">Se indicó cómo prevenir que el problema se vuelva a presentar</td>
            <td><?php echo htmlspecialchars($row['capacitacionPrevencion']); ?></td>
        </tr>
        <tr>
            <td colspan="3">Se indicó cómo actuar en caso de que el problema se vuelva a presentar</td>
            <td><?php echo htmlspecialchars($row['capacitacionAccion']); ?></td>
        </tr>

        <!-- Constancia de Realización de Asistencia -->
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
        <?php if (!empty($row['contratista2'])): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['contratista2']); ?></td>
            <td><?php echo htmlspecialchars($row['cedula2']); ?></td>
            <td><?php echo htmlspecialchars($row['horaEntrada2']); ?></td>
            <td><?php echo htmlspecialchars($row['horaSalida2']); ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($row['contratista3'])): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['contratista3']); ?></td>
            <td><?php echo htmlspecialchars($row['cedula3']); ?></td>
            <td><?php echo htmlspecialchars($row['horaEntrada3']); ?></td>
            <td><?php echo htmlspecialchars($row['horaSalida3']); ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($row['contratista4'])): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['contratista4']); ?></td>
            <td><?php echo htmlspecialchars($row['cedula4']); ?></td>
            <td><?php echo htmlspecialchars($row['horaEntrada4']); ?></td>
            <td><?php echo htmlspecialchars($row['horaSalida4']); ?></td>
        </tr>
        <?php endif; ?>

        <!-- Información del Funcionario -->
        <tr class="section-header">
            <td colspan="4"><center>FUNCIONARIO</center></td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td><?php echo htmlspecialchars($row['nombreFuncionario']); ?></td>
            <th>Cédula</th>
            <td><?php echo htmlspecialchars($row['cedulaFuncionario']); ?></td>
        </tr>
        <tr>
            <th>Cargo</th>
            <td><?php echo htmlspecialchars($row['cargoFuncionario']); ?></td>
            <th>SAP</th>
            <td><?php echo htmlspecialchars($row['sapFuncionario']); ?></td>
        </tr>

        <!-- Firmas -->
        <tr class="section-header">
            <td colspan="4"><center>FIRMAS</center></td>
        </tr>
        <tr>
            <th>Firma del Cliente</th>
            <td>
                <?php if (!empty($row['firma_digital_cliente'])): ?>
                    <img src="<?php echo htmlspecialchars($row['firma_digital_cliente']); ?>" alt="Firma del Cliente" width="200">
                <?php else: ?>
                    No disponible
                <?php endif; ?>
            </td>
            <th>Firma del Técnico</th>
            <td>
                <?php if (!empty($row['firma_digital_tecnico'])): ?>
                    <img src="<?php echo htmlspecialchars($row['firma_digital_tecnico']); ?>" alt="Firma del Técnico" width="200">
                <?php else: ?>
                    No disponible
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Reemplazar la sección de imágenes en tu archivo -->

<h2>Imágenes Asociadas</h2>
<div class="image-container">
    <?php if ($resultado_imagenes->num_rows > 0): ?>
        <?php 
        $imagen_index = 0;
        mysqli_data_seek($resultado_imagenes, 0); // Resetear el puntero del resultado
        while ($img_row = $resultado_imagenes->fetch_assoc()): 
        ?>
            <div class="image-thumbnail" onclick="openImageModal(<?php echo $imagen_index; ?>)" style="cursor: pointer;">
                <img src="<?php echo $url_sys . htmlspecialchars($img_row['imagen']); ?>" 
                     alt="Imagen asociada" 
                     class="gallery" 
                     style="max-width: 150px; margin: 5px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s;"
                     onmouseover="this.style.transform='scale(1.05)'"
                     onmouseout="this.style.transform='scale(1)'">
            </div>
        <?php 
            $imagen_index++;
        endwhile; 
        ?>
    <?php else: ?>
        <p>No hay imágenes asociadas a este formulario.</p>
    <?php endif; ?>
</div>

<!-- Modal para mostrar imágenes en slider -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content" style="background: transparent; border: none;">
            <div class="modal-header" style="border: none; position: absolute; right: 0; z-index: 1051;">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 2rem; text-shadow: 0 0 10px rgba(0,0,0,0.5); opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="imageCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
                    <!-- Indicadores -->
                    <ol class="carousel-indicators">
                        <?php 
                        mysqli_data_seek($resultado_imagenes, 0);
                        $indicator_index = 0;
                        while ($img_row = $resultado_imagenes->fetch_assoc()): 
                        ?>
                            <li data-target="#imageCarousel" 
                                data-slide-to="<?php echo $indicator_index; ?>" 
                                class="<?php echo $indicator_index === 0 ? 'active' : ''; ?>">
                            </li>
                        <?php 
                            $indicator_index++;
                        endwhile; 
                        ?>
                    </ol>

                    <!-- Slides -->
                    <div class="carousel-inner">
                        <?php 
                        mysqli_data_seek($resultado_imagenes, 0);
                        $slide_index = 0;
                        while ($img_row = $resultado_imagenes->fetch_assoc()): 
                        ?>
                            <div class="carousel-item <?php echo $slide_index === 0 ? 'active' : ''; ?>">
                                <div class="d-flex justify-content-center align-items-center" style="min-height: 70vh;">
                                    <img src="<?php echo $url_sys . htmlspecialchars($img_row['imagen']); ?>" 
                                         class="d-block" 
                                         alt="Imagen <?php echo $slide_index + 1; ?>"
                                         style="max-width: 100%; max-height: 85vh; object-fit: contain; border-radius: 8px;">
                                </div>
                                <div class="carousel-caption d-block" style="background: rgba(0,0,0,0.5); border-radius: 8px; padding: 10px;">
                                    <p>Imagen <?php echo $slide_index + 1; ?> de <?php echo $resultado_imagenes->num_rows; ?></p>
                                </div>
                            </div>
                        <?php 
                            $slide_index++;
                        endwhile; 
                        ?>
                    </div>

                    <!-- Controles -->
                    <a class="carousel-control-prev" href="#imageCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: drop-shadow(0 0 5px rgba(0,0,0,0.5));"></span>
                        <span class="sr-only">Anterior</span>
                    </a>
                    <a class="carousel-control-next" href="#imageCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="filter: drop-shadow(0 0 5px rgba(0,0,0,0.5));"></span>
                        <span class="sr-only">Siguiente</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para abrir el modal en una imagen específica
function openImageModal(index) {
    $('#imageCarousel').carousel(index);
    $('#imageModal').modal('show');
}

// Soporte para teclas de flechas en el modal
$(document).ready(function() {
    $('#imageModal').on('shown.bs.modal', function () {
        $(document).on('keydown.imageModal', function(e) {
            if (e.keyCode === 37) { // Flecha izquierda
                $('#imageCarousel').carousel('prev');
            } else if (e.keyCode === 39) { // Flecha derecha
                $('#imageCarousel').carousel('next');
            } else if (e.keyCode === 27) { // Escape
                $('#imageModal').modal('hide');
            }
        });
    });
    
    $('#imageModal').on('hidden.bs.modal', function () {
        $(document).off('keydown.imageModal');
    });
});
</script>

<style>
/* Estilos adicionales para el modal de imágenes */
#imageModal .modal-dialog {
    max-width: 95vw;
}

#imageModal .carousel-control-prev-icon,
#imageModal .carousel-control-next-icon {
    width: 3rem;
    height: 3rem;
}

#imageModal .carousel-indicators li {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(255,255,255,0.5);
}

#imageModal .carousel-indicators .active {
    background-color: white;
}

.image-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 30px;
}

.image-thumbnail img:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.2) !important;
}

/* Responsive */
@media (max-width: 768px) {
    #imageModal .modal-dialog {
        max-width: 100vw;
        margin: 0;
    }
    
    #imageModal .carousel-inner img {
        max-height: 60vh;
    }
}
</style>
	
    <?php
}
?>
	
	 <!-- Modal de Confirmación de aprobacion -->
                <div class="modal fade" id="aproveModal" tabindex="-1" aria-labelledby="aproveModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="approve.php?id=<?php echo htmlspecialchars($formulario_id); ?><?php echo $navigation_query ? '&' . htmlspecialchars($navigation_query) : ''; ?>" method="POST">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="aproveModalLabel">Confirmar Aprobación</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
							<center><img src="https://cdn-icons-gif.flaticon.com/10826/10826774.gif" width="150px"></center>
                          <!-- Campo oculto para pasar el ID del usuario -->
                          <input type="hidden" name="id" value="<?php echo htmlspecialchars($formulario_id); ?>">
                          <!-- Campo oculto para CSRF Token -->
                          <p>¿Está seguro de que desea aprobar el formulario <strong><?php echo htmlspecialchars($formulario_id); ?></strong>?</p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                          <button type="submit" class="btn btn-success"><i class="fa fa-check-circle fa-lg"></i> Si, Aprobar</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
	
	
	<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="delete.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
					<center><img src="https://cdn-icons-gif.flaticon.com/6172/6172548.gif" width="150px"></center>
                    <!-- Campo oculto para el ID del formulario -->
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($formulario_id); ?>">
                    <!-- Campo oculto para el token CSRF fijo -->
                    <input type="hidden" name="csrf_token" value="FoKjYUV5DwBSQ8eOoozVkxKjMpIcZtCTgDwxhVeMAPj5k1dI35cZ0R9XP8vp6ChwPxsWWlLIkeSXuvyorYhMBJaPPh3vku2lsmTidLcZgELxEQj6ce784JVlI4we3yasGEE2qYvmfLiJPXb3lgGD8xktIerJTteFR8189o2RpcvrxcujzcqkPkAS0fDOrUF2ofbXOWUPfPWq5jReQ5lchyVkHuJC6yI9eixckQ0rZSXVwYiqVG2aWLP3SYzrB5bMqOb9ZgB0awoKzkw8bLs0eqgtB2uSccUbhI8qrtL4oR9zUM1HEtxSkxZOkWVUFPx4Bzzl7uicqpvxKrws9iEmywaeESifPLWRXtTTqxTQg4RJh7DGKFBfOU2Eho7IsdT5Hfh8zogIGHB8IKgRcu7HAx1mOIF5GRcgUwv7XML5IV0cqYIc6emv8Ubuy1oZEdUeH7EpeC7SCcZBO2O4oad6MxRTYALD3kSVrdp0tA61cLQJV3xfkRiD4vFflmfadqpR">
                    <p>¿Está seguro de que desea eliminar el formulario <strong><?php echo htmlspecialchars($formulario_id); ?></strong>? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash fa-lg"></i> Sí, Eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>

	
	
	<!-- Modal de Cargando PDF -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loadingModalLabel">Generando PDF</h5>
            </div>
            <div class="modal-body text-center">
                <p>Por favor espere mientras se genera el PDF...</p>
                <img src="https://cdn-icons-gif.flaticon.com/17110/17110686.gif" width="150px" alt="Cargando...">
            </div>
        </div>
    </div>
</div>

	
	
<!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <!-- Incluir Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" crossorigin="anonymous"></script>

    <!-- Incluir Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Inicializar Tooltips de Bootstrap -->
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip(); 
        });
    </script>
	
	<script>
function generarPDF(url, id) {
    // Mostrar el modal de carga
    $('#loadingModal').modal('show');

    // Iniciar la descarga del PDF manualmente con fetch
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Error al generar el PDF');
            return response.blob(); // Convertir la respuesta en un blob
        })
        .then(blob => {
            // Crear una URL temporal para el archivo PDF
            const pdfUrl = URL.createObjectURL(blob);
            
            // Crear un enlace invisible y forzar la descarga con el nombre dinámico
            const a = document.createElement('a');
            a.href = pdfUrl;
            a.download = `FormularioMantenimientoNo${id}.pdf`; // Nombre dinámico
            document.body.appendChild(a);
            a.click(); // Simular un clic en el enlace
            document.body.removeChild(a);

            // Ocultar el modal cuando se inicie la descarga
            $('#loadingModal').modal('hide');
        })
        .catch(error => {
            console.error('Error al generar el PDF:', error);
            alert('Hubo un problema al generar el PDF.');
            $('#loadingModal').modal('hide'); // Asegurar que se oculta si hay un error
        });
}
</script>







<?php include('../inc/menu-foot.php'); ?>
	

<?php include('../inc/footer.php'); ?>
<!-- Lightbox2 JS -->
<!--script src="../js/lightbox-plus-jquery.min.js"></script--s>
</body>
</html>

<?php
$conexion->close();
?>









