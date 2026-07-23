<?php include('../login/sesion.php');?>
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

if ($formulario_id > 0) {
    // Consulta SQL para obtener los datos del formulario, incluyendo el estado
    $sql_formulario = "SELECT 
                f.id, 
                f.nombreSolicitante, 
                f.cargo, 
                f.nombreTienda, 
                f.numeroTienda, 
                f.numeroTicket, 
                f.fecha, 
                f.municipio, 
                f.departamento, 
                f.nombreEquipo, 
                f.marca, 
                f.serial, 
                f.descripcionFalla, 
                f.diagnosticoTecnico, 
                f.repuestosCambiados, 
                f.observaciones, 
                f.seguridadRiesgoAccidentalidad, 
                f.seguridadRiesgoEquipo, 
                f.funcionamientoFallaSolucionada, 
                f.funcionamientoPasosNormales, 
                f.calidadTrabajo, 
                f.limpiezaOrganizacionArmado, 
                f.limpiezaOrganizacionAseado, 
                f.capacitacionCausa, 
                f.capacitacionPrevencion, 
                f.capacitacionAccion, 
                f.contratista1, 
                f.cedula1, 
                f.horaEntrada1, 
                f.horaSalida1, 
                f.contratista2, 
                f.cedula2, 
                f.horaEntrada2, 
                f.horaSalida2, 
                f.contratista3, 
                f.cedula3, 
                f.horaEntrada3, 
                f.horaSalida3, 
                f.contratista4, 
                f.cedula4, 
                f.horaEntrada4, 
                f.horaSalida4, 
                f.nombreFuncionario, 
                f.cedulaFuncionario, 
                f.cargoFuncionario, 
                f.sapFuncionario,
                f.asistenciaReparacion,
                f.asistenciaGarantia,
                f.asistenciaAjuste,
                f.asistenciaModificacion,
                f.asistenciaServicio,
                f.asistenciaMejora,
                f.asistenciaCombinacion,
                f.fallaOperacion,
                f.fallaMecanica,
                f.fallaElectrica,
                f.fallaTerceros,
                f.fallaFabricacion,
                f.firma_digital_cliente, 
                f.firma_digital_tecnico,
                f.estado
            FROM formulario f
            WHERE f.id = $formulario_id";

    $resultado_formulario = $conexion->query($sql_formulario);

    if ($resultado_formulario->num_rows > 0) {
        $row = $resultado_formulario->fetch_assoc(); // Obtener el registro

        // Obtener el departamento del formulario
        $departamento_formulario = $row['departamento'];

        // Obtener las zonas del usuario desde la sesión
        $zonas_usuario = $_SESSION['zonas']; // Esto es una cadena con las zonas separadas por comas

        // Convertir las zonas del usuario en un array
        $zonas_array = explode(',', $zonas_usuario);
        $zonas_array = array_map('trim', $zonas_array); // Eliminar espacios en blanco

        // Verificar si el usuario tiene permiso para editar este formulario
        if (in_array($departamento_formulario, $zonas_array)) {
            // El usuario tiene permiso, continuar
            $tiene_permiso = true;
        } else {
            // El usuario no tiene permiso
            $tiene_permiso = false;
        }

        // Verificar si el formulario está aprobado
        $formulario_aprobado = ($row['estado'] == 1);

        // Consulta SQL para obtener las imágenes asociadas al formulario
        $sql_imagenes = "SELECT * FROM imagenes WHERE formulario_id = $formulario_id";
        $resultado_imagenes = $conexion->query($sql_imagenes);
    } else {
        echo "<p>No se encontraron datos para el ID de formulario proporcionado.</p>";
        exit;
    }
} else {
    die("ID de formulario no válido.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Edición de Formulario</title>
   <?php include('../inc/header.php');?>
    <link rel="stylesheet" href="../css/style.css?<?php echo time();?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/departamentos.js"></script>
	 <!-- Incluir Bootstrap CSS para estilos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<!-- Estilos personalizados -->
	




<style>
    /* Estilos para el contenedor del campo de subida de imágenes */
    .custom-file-upload {
        margin-bottom: 20px;
    }

    /* Estilo para el label */
    .custom-label {
        font-weight: bold;
        color: #000;
        margin-bottom: 10px;
        display: block;
    }

    .custom-file-upload {
    margin-bottom: 15px;
}

.custom-label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
    font-size: 1.1rem;
}

#drop-zone {
    border: 2px dashed #86C724;
    border-radius: 14px;
    padding: 36px 20px;
    text-align: center;
    cursor: pointer;
    background: #f9fff4;
    transition: background 0.25s, border-color 0.25s, box-shadow 0.25s;
    user-select: none;
}

#drop-zone:hover {
    background: #edffd4;
    border-color: #5FCA00;
    box-shadow: 0 0 0 4px rgba(134,199,36,0.15);
}

#drop-zone.drag-over {
    background: #dffab8;
    border-color: #4db800;
    box-shadow: 0 0 0 6px rgba(134,199,36,0.25);
}

#drop-zone .drop-icon {
    font-size: 2.6rem;
    color: #86C724;
    margin-bottom: 10px;
    display: block;
    transition: transform 0.2s;
}

#drop-zone:hover .drop-icon,
#drop-zone.drag-over .drop-icon {
    transform: translateY(-4px);
}

#drop-zone .drop-title {
    font-size: 1rem;
    font-weight: 700;
    color: #333;
}

#drop-zone .drop-sub {
    font-size: 0.82rem;
    color: #888;
    margin-top: 4px;
}

#drop-zone .drop-count {
    display: inline-block;
    margin-top: 10px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #fff;
    background: #86C724;
    border-radius: 20px;
    padding: 2px 12px;
}

.preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.preview-container img {
    max-width: 150px;
    max-height: 150px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 5px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
}

.preview-container img:hover {
    transform: scale(1.05);
}


    /* Estilo para el botón de envío */
    .custom-submit-button {
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s, box-shadow 0.3s;
    }

    .custom-submit-button:hover {
        background-color: #0056b3;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .custom-submit-button:active {
        background-color: #004080;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
	
	/* Estilos personalizados para el modal */
.modal-custom {
    position: fixed;
    top: 10%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 500px;
    z-index: 1050;
    display: none;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    opacity: 0;  /* Para animación */
    transition: opacity 0.5s ease, top 0.5s ease; /* Para la animación */
}

.modal-custom.show {
    opacity: 1;
    top: 50%;
}

/* Fondo opaco para el modal */
.modal-backdrop-custom {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    display: none;
}

/* Encabezado del modal */
.modal-header-custom {
    padding: 15px;
    background-color: #f5f5f5;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title-custom {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.close-custom {
    border: none;
    background: none;
    font-size: 1.5rem;
    cursor: pointer;
}

/* Cuerpo del modal */
.modal-body-custom {
    padding: 20px;
    font-size: 1rem;
    line-height: 1.5;
}

/* Pie del modal */
.modal-footer-custom {
    padding: 15px;
    background-color: #f5f5f5;
    border-top: 1px solid #dee2e6;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.modal-footer-custom .btn {
    padding: 10px 20px;
    font-size: 1rem;
    border-radius: 5px;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s;
}

/* Botón cancelar */
.btn-secondary-custom {
    background-color: #6c757d;
    color: white;
}

.btn-secondary-custom:hover {
    background-color: #5a6268;
}

/* Botón de confirmación */
.btn-danger-custom {
    background-color: #dc3545;
    color: white;
}

.btn-danger-custom:hover {
    background-color: #c82333;
}
	
	form textarea{
		width: 100%;
		height: 200px;
	}



	
/* Estilos personalizados para botones */
.btn-group .btn {
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

.custom-button {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}

.custom-submit-button {
    background-color: #007bff; /* Azul Bootstrap */
    color: white;
    border: none;
}

.custom-submit-button:hover {
    background-color: #0056b3;
}

.custom-cancel-button {
    background-color: #dc3545; /* Rojo Bootstrap */
    color: white;
    border: none;
}

.custom-cancel-button:hover {
    background-color: #c82333;
}



	
</style>
	
</head>
<body>
<?php include('../inc/menu.php');?>

<?php
if (isset($tiene_permiso) && !$tiene_permiso) {
    // Mostrar el mensaje después del menú
    echo "<div class='alert alert-danger text-center'>No tiene permisos para editar este formulario.</div>";
} else {
    // Mostrar el contenido del formulario
    ?>
    <h1>Editar Formulario de Mantenimiento</h1>

    <?php if ($formulario_aprobado): ?>
        <p style="color: red; font-weight: bold;">Este formulario ya ha sido aprobado y no se puede editar.</p>
    <?php endif; ?>

    <?php if (!$formulario_aprobado): ?>
	
    <form action="update.php<?php echo $navigation_query ? '?' . htmlspecialchars($navigation_query) : ''; ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="formulario_id" value="<?php echo $row['id']; ?>">
        <!-- Sección de Solicitante -->
        <table>
            <tr class="section-header">
                <td colspan="4"><center>SOLICITANTE Y TIENDA BENEFICIARIA</center></td>
            </tr>
            <tr>
                <th>Nombre del Solicitante</th>
                <td><input type="text" name="nombreSolicitante" value="<?php echo htmlspecialchars($row['nombreSolicitante']); ?>"></td>
                <th>Cargo</th>
                <td><input type="text" name="cargo" value="<?php echo htmlspecialchars($row['cargo']); ?>"></td>
            </tr>
            <tr>
                <th>Nombre de la Tienda</th>
                <td><input type="text" name="nombreTienda" value="<?php echo htmlspecialchars($row['nombreTienda']); ?>"></td>
                <th>Número de Tienda</th>
                <td><input type="text" name="numeroTienda" value="<?php echo htmlspecialchars($row['numeroTienda']); ?>"></td>
            </tr>
            <tr>
                <th>Número de Ticket</th>
                <td><input type="text" name="numeroTicket" value="<?php echo htmlspecialchars($row['numeroTicket']); ?>"></td>
                <th>Fecha</th>
                <td><input type="date" name="fecha" value="<?php echo htmlspecialchars($row['fecha']); ?>"></td>
            </tr>
            <tr>
                <th>Departamento</th>
            <td>
                <select name="departamento" class="form-control" id="departamento" onchange="cargarCiudades()" required>
                    <option value="" <?php echo ($row['departamento'] == '') ? 'selected' : ''; ?>>Seleccione un departamento</option>
                    <option value="Amazonas" <?php echo ($row['departamento'] == 'Amazonas') ? 'selected' : ''; ?>>Amazonas</option>
                    <option value="Antioquia" <?php echo ($row['departamento'] == 'Antioquia') ? 'selected' : ''; ?>>Antioquia</option>
                    <option value="Arauca" <?php echo ($row['departamento'] == 'Arauca') ? 'selected' : ''; ?>>Arauca</option>
                    <option value="Atlántico" <?php echo ($row['departamento'] == 'Atlántico') ? 'selected' : ''; ?>>Atlántico</option>
                    <option value="Bolívar" <?php echo ($row['departamento'] == 'Bolívar') ? 'selected' : ''; ?>>Bolívar</option>
                    <option value="Boyacá" <?php echo ($row['departamento'] == 'Boyacá') ? 'selected' : ''; ?>>Boyacá</option>
                    <option value="Caldas" <?php echo ($row['departamento'] == 'Caldas') ? 'selected' : ''; ?>>Caldas</option>
                    <option value="Caquetá" <?php echo ($row['departamento'] == 'Caquetá') ? 'selected' : ''; ?>>Caquetá</option>
                    <option value="Casanare" <?php echo ($row['departamento'] == 'Casanare') ? 'selected' : ''; ?>>Casanare</option>
                    <option value="Cauca" <?php echo ($row['departamento'] == 'Cauca') ? 'selected' : ''; ?>>Cauca</option>
                    <option value="Cesar" <?php echo ($row['departamento'] == 'Cesar') ? 'selected' : ''; ?>>Cesar</option>
                    <option value="Chocó" <?php echo ($row['departamento'] == 'Chocó') ? 'selected' : ''; ?>>Chocó</option>
                    <option value="Córdoba" <?php echo ($row['departamento'] == 'Córdoba') ? 'selected' : ''; ?>>Córdoba</option>
                    <option value="Cundinamarca" <?php echo ($row['departamento'] == 'Cundinamarca') ? 'selected' : ''; ?>>Cundinamarca</option>
                    <option value="Guainía" <?php echo ($row['departamento'] == 'Guainía') ? 'selected' : ''; ?>>Guainía</option>
                    <option value="Guaviare" <?php echo ($row['departamento'] == 'Guaviare') ? 'selected' : ''; ?>>Guaviare</option>
                    <option value="Huila" <?php echo ($row['departamento'] == 'Huila') ? 'selected' : ''; ?>>Huila</option>
                    <option value="La Guajira" <?php echo ($row['departamento'] == 'La Guajira') ? 'selected' : ''; ?>>La Guajira</option>
                    <option value="Magdalena" <?php echo ($row['departamento'] == 'Magdalena') ? 'selected' : ''; ?>>Magdalena</option>
                    <option value="Meta" <?php echo ($row['departamento'] == 'Meta') ? 'selected' : ''; ?>>Meta</option>
                    <option value="Nariño" <?php echo ($row['departamento'] == 'Nariño') ? 'selected' : ''; ?>>Nariño</option>
                    <option value="Norte de Santander" <?php echo ($row['departamento'] == 'Norte de Santander') ? 'selected' : ''; ?>>Norte de Santander</option>
                    <option value="Putumayo" <?php echo ($row['departamento'] == 'Putumayo') ? 'selected' : ''; ?>>Putumayo</option>
                    <option value="Quindío" <?php echo ($row['departamento'] == 'Quindío') ? 'selected' : ''; ?>>Quindío</option>
                    <option value="Risaralda" <?php echo ($row['departamento'] == 'Risaralda') ? 'selected' : ''; ?>>Risaralda</option>
                    <option value="San Andrés y Providencia" <?php echo ($row['departamento'] == 'San Andrés y Providencia') ? 'selected' : ''; ?>>San Andrés y Providencia</option>
                    <option value="Santander" <?php echo ($row['departamento'] == 'Santander') ? 'selected' : ''; ?>>Santander</option>
                    <option value="Sucre" <?php echo ($row['departamento'] == 'Sucre') ? 'selected' : ''; ?>>Sucre</option>
                    <option value="Tolima" <?php echo ($row['departamento'] == 'Tolima') ? 'selected' : ''; ?>>Tolima</option>
                    <option value="Valle del Cauca" <?php echo ($row['departamento'] == 'Valle del Cauca') ? 'selected' : ''; ?>>Valle del Cauca</option>
                    <option value="Vaupés" <?php echo ($row['departamento'] == 'Vaupés') ? 'selected' : ''; ?>>Vaupés</option>
                    <option value="Vichada" <?php echo ($row['departamento'] == 'Vichada') ? 'selected' : ''; ?>>Vichada</option>
                </select><br>
            </td>
                <!-- Municipio -->
                <th>Municipio</th>
                <td>
                    <select name="municipio" id="municipio" class="form-control" required data-municipio="<?php echo htmlspecialchars($row['municipio']); ?>">
                        <option value="" disabled selected>Seleccione</option>
                        <!-- Opciones de ciudades se cargarán aquí por JavaScript -->
                    </select>
                </td>
            </tr>

            <!-- Sección de Tipo de Asistencia -->
            <tr class="section-header">
                <td colspan="4"><center>TIPO DE ASISTENCIA</center></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="asistenciaReparacion" value="1" <?php echo ($row['asistenciaReparacion'] == 1) ? 'checked' : ''; ?>> Reparación</td>
                <td><input type="checkbox" name="asistenciaGarantia" value="1" <?php echo ($row['asistenciaGarantia'] == 1) ? 'checked' : ''; ?>> Garantía</td>
                <td><input type="checkbox" name="asistenciaAjuste" value="1" <?php echo ($row['asistenciaAjuste'] == 1) ? 'checked' : ''; ?>> Ajuste</td>
                <td><input type="checkbox" name="asistenciaModificacion" value="1" <?php echo ($row['asistenciaModificacion'] == 1) ? 'checked' : ''; ?>> Modificación</td>
            </tr>
            <tr>
                <td><input type="checkbox" name="asistenciaServicio" value="1" <?php echo ($row['asistenciaServicio'] == 1) ? 'checked' : ''; ?>> Servicio</td>
                <td><input type="checkbox" name="asistenciaMejora" value="1" <?php echo ($row['asistenciaMejora'] == 1) ? 'checked' : ''; ?>> Mejora</td>
                <td><input type="checkbox" name="asistenciaCombinacion" value="1" <?php echo ($row['asistenciaCombinacion'] == 1) ? 'checked' : ''; ?>> Combinación</td>
                <td></td>
            </tr>

            <!-- Sección de Tipo/Causa de Fallas Básicas -->
            <tr class="section-header">
                <td colspan="4"><center>TIPO/CAUSA DE FALLAS BÁSICAS</center></td>
            </tr>
            <tr>
                <td><input type="checkbox" name="fallaOperacion" value="1" <?php echo ($row['fallaOperacion'] == 1) ? 'checked' : ''; ?>> Operación</td>
                <td><input type="checkbox" name="fallaMecanica" value="1" <?php echo ($row['fallaMecanica'] == 1) ? 'checked' : ''; ?>> Mecánica</td>
                <td><input type="checkbox" name="fallaElectrica" value="1" <?php echo ($row['fallaElectrica'] == 1) ? 'checked' : ''; ?>> Eléctrica</td>
                <td><input type="checkbox" name="fallaTerceros" value="1" <?php echo ($row['fallaTerceros'] == 1) ? 'checked' : ''; ?>> Terceros</td>
            </tr>
            <tr>
                <td><input type="checkbox" name="fallaFabricacion" value="1" <?php echo ($row['fallaFabricacion'] == 1) ? 'checked' : ''; ?>> Fabricación</td>
                <td colspan="3"></td>
            </tr>

            <!-- Sección de Información Área Técnica -->
            <tr class="section-header">
                <td colspan="4"><center>INFORMACIÓN ÁREA TÉCNICA</center></td>
            </tr>
            <tr>
                <th>Nombre del Equipo</th>
                <td><input type="text" name="nombreEquipo" value="<?php echo htmlspecialchars($row['nombreEquipo']); ?>"></td>
                <th>Marca</th>
                <td><input type="text" name="marca" value="<?php echo htmlspecialchars($row['marca']); ?>"></td>
            </tr>
            <tr>
                <th>Serial</th>
                <td colspan="3"><input type="text" name="serial" value="<?php echo htmlspecialchars($row['serial']); ?>"></td>
            </tr>
            <tr>
                <th>Descripción de la Falla</th>
                <td colspan="3"><textarea name="descripcionFalla"><?php echo htmlspecialchars($row['descripcionFalla']); ?></textarea></td>
            </tr>
            <tr>
                <th>Diagnóstico Técnico</th>
                <td colspan="3"><textarea name="diagnosticoTecnico"><?php echo htmlspecialchars($row['diagnosticoTecnico']); ?></textarea></td>
            </tr>
            <tr>
                <th>Repuestos Cambiados</th>
                <td colspan="3"><textarea name="repuestosCambiados"><?php echo htmlspecialchars($row['repuestosCambiados']); ?></textarea></td>
            </tr>
            <tr>
                <th>Observaciones</th>
                <td colspan="3"><textarea name="observaciones"><?php echo htmlspecialchars($row['observaciones']); ?></textarea></td>
            </tr>

            <!-- Sección de Evaluación del Servicio -->
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
                <td>
                    <select name="seguridadRiesgoAccidentalidad">
                        <option value="Cumple" <?php echo ($row['seguridadRiesgoAccidentalidad'] == 'Cumple') ? 'selected' : ''; ?>>Cumple</option>
                        <option value="No Cumple" <?php echo ($row['seguridadRiesgoAccidentalidad'] == 'No Cumple') ? 'selected' : ''; ?>>No Cumple</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="3">La labor realizada ofrece algún riesgo para la integridad del equipo</td>
                <td>
                    <select name="seguridadRiesgoEquipo">
                        <option value="Cumple" <?php echo ($row['seguridadRiesgoEquipo'] == 'Cumple') ? 'selected' : ''; ?>>Cumple</option>
                        <option value="No Cumple" <?php echo ($row['seguridadRiesgoEquipo'] == 'No Cumple') ? 'selected' : ''; ?>>No Cumple</option>
                    </select>
                </td>
            </tr>

            <tr class="section-header">
                <td colspan="4"><center>FUNCIONALIDAD</center></td>
            </tr>
            <tr>
                <td colspan="3">La falla reportada fue solucionada con el trabajo realizado</td>
                <td>
                    <select name="funcionamientoFallaSolucionada">
                        <option value="Cumple" <?php echo ($row['funcionamientoFallaSolucionada'] == 'Cumple') ? 'selected' : ''; ?>>Cumple</option>
                        <option value="No Cumple" <?php echo ($row['funcionamientoFallaSolucionada'] == 'No Cumple') ? 'selected' : ''; ?>>No Cumple</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="3">Los pasos normales de manejo se siguen sin procedimientos extra</td>
                <td>
                    <select name="funcionamientoPasosNormales">
                        <option value="Cumple" <?php echo ($row['funcionamientoPasosNormales'] == 'Cumple') ? 'selected' : ''; ?>>Cumple</option>
                        <option value="No Cumple" <?php echo ($row['funcionamientoPasosNormales'] == 'No Cumple') ? 'selected' : ''; ?>>No Cumple</option>
                    </select>
                </td>
            </tr>

            <tr class="section-header">
                <td colspan="4"><center>CALIDAD</center></td>
            </tr>
            <tr>
                <td colspan="3">La calidad del trabajo fue adecuada</td>
                <td>
                    <select name="calidadTrabajo">
                        <option value="Cumple" <?php echo ($row['calidadTrabajo'] == 'Cumple') ? 'selected' : ''; ?>>Cumple</option>
                        <option value="No Cumple" <?php echo ($row['calidadTrabajo'] == 'No Cumple') ? 'selected' : ''; ?>>No Cumple</option>
                    </select>
                </td>
            </tr>

            <tr class="section-header">
                <td colspan="4"><center>LIMPIEZA</center></td>
            </tr>
            <tr>
                <td colspan="3">El área intervenida fue dejada organizada y aseada</td>
                <td>
                    <select name="limpiezaOrganizacionArmado">
                        <option value="Cumple" <?php echo ($row['limpiezaOrganizacionArmado'] == 'Cumple') ? 'selected' : ''; ?>>Cumple</option>
                        <option value="No Cumple" <?php echo ($row['limpiezaOrganizacionArmado'] == 'No Cumple') ? 'selected' : ''; ?>>No Cumple</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="3">Los escombros fueron limpiados</td>
                <td>
                    <select name="limpiezaOrganizacionAseado">
                        <option value="Cumple" <?php echo ($row['limpiezaOrganizacionAseado'] == 'Cumple') ? 'selected' : ''; ?>>Cumple</option>
                        <option value="No Cumple" <?php echo ($row['limpiezaOrganizacionAseado'] == 'No Cumple') ? 'selected' : ''; ?>>No Cumple</option>
                    </select>
                </td>
            </tr>

            <!-- Sección de Capacitación -->
            <tr class="section-header">
                <td colspan="4"><center>CAPACITACIÓN</center></td>
            </tr>
            <tr>
                <td colspan="3">Se indicó la causa de la novedad al personal que recibió el trabajo</td>
                <td>
                    <select name="capacitacionCausa">
                        <option value="Cumple" <?php echo ($row['capacitacionCausa'] == 'Cumple') ? 'selected' : ''; ?>>Cumple</option>
                        <option value="No Cumple" <?php echo ($row['capacitacionCausa'] == 'No Cumple') ? 'selected' : ''; ?>>No Cumple</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="3">Se indicó cómo prevenir que el problema se vuelva a presentar</td>
                <td>
                    <select name="capacitacionPrevencion">
                        <option value="Cumple" <?php echo ($row['capacitacionPrevencion'] == 'Cumple') ? 'selected' : ''; ?>>Cumple</option>
                        <option value="No Cumple" <?php echo ($row['capacitacionPrevencion'] == 'No Cumple') ? 'selected' : ''; ?>>No Cumple</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="3">Se indicó cómo actuar en caso de que el problema se vuelva a presentar</td>
                <td>
                    <select name="capacitacionAccion">
                        <option value="Cumple" <?php echo ($row['capacitacionAccion'] == 'Cumple') ? 'selected' : ''; ?>>Cumple</option>
                        <option value="No Cumple" <?php echo ($row['capacitacionAccion'] == 'No Cumple') ? 'selected' : ''; ?>>No Cumple</option>
                    </select>
                </td>
            </tr>

            <!-- Sección de Constancia de Realización de Asistencia -->
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
                <td><input type="text" name="contratista1" value="<?php echo htmlspecialchars($row['contratista1']); ?>"></td>
                <td><input type="text" name="cedula1" value="<?php echo htmlspecialchars($row['cedula1']); ?>"></td>
                <td><input type="time" name="horaEntrada1" value="<?php echo htmlspecialchars($row['horaEntrada1']); ?>"></td>
                <td><input type="time" name="horaSalida1" value="<?php echo htmlspecialchars($row['horaSalida1']); ?>"></td>
            </tr>
            <tr>
                <td><input type="text" name="contratista2" value="<?php echo htmlspecialchars($row['contratista2']); ?>"></td>
                <td><input type="text" name="cedula2" value="<?php echo htmlspecialchars($row['cedula2']); ?>"></td>
                <td><input type="time" name="horaEntrada2" value="<?php echo htmlspecialchars($row['horaEntrada2']); ?>"></td>
                <td><input type="time" name="horaSalida2" value="<?php echo htmlspecialchars($row['horaSalida2']); ?>"></td>
            </tr>
            <tr>
                <td><input type="text" name="contratista3" value="<?php echo htmlspecialchars($row['contratista3']); ?>"></td>
                <td><input type="text" name="cedula3" value="<?php echo htmlspecialchars($row['cedula3']); ?>"></td>
                <td><input type="time" name="horaEntrada3" value="<?php echo htmlspecialchars($row['horaEntrada3']); ?>"></td>
                <td><input type="time" name="horaSalida3" value="<?php echo htmlspecialchars($row['horaSalida3']); ?>"></td>
            </tr>
            <tr>
                <td><input type="text" name="contratista4" value="<?php echo htmlspecialchars($row['contratista4']); ?>"></td>
                <td><input type="text" name="cedula4" value="<?php echo htmlspecialchars($row['cedula4']); ?>"></td>
                <td><input type="time" name="horaEntrada4" value="<?php echo htmlspecialchars($row['horaEntrada4']); ?>"></td>
                <td><input type="time" name="horaSalida4" value="<?php echo htmlspecialchars($row['horaSalida4']); ?>"></td>
            </tr>

            <!-- Sección de Funcionario -->
            <tr class="section-header">
                <td colspan="4"><center>FUNCIONARIO</center></td>
            </tr>
            <tr>
                <th>Nombre</th>
                <td><input type="text" name="nombreFuncionario" value="<?php echo htmlspecialchars($row['nombreFuncionario']); ?>"></td>
                <th>Cédula</th>
                <td><input type="text" name="cedulaFuncionario" value="<?php echo htmlspecialchars($row['cedulaFuncionario']); ?>"></td>
            </tr>
            <tr>
                <th>Cargo</th>
                <td><input type="text" name="cargoFuncionario" value="<?php echo htmlspecialchars($row['cargoFuncionario']); ?>"></td>
                <th>SAP</th>
                <td><input type="text" name="sapFuncionario" value="<?php echo htmlspecialchars($row['sapFuncionario']); ?>"></td>
            </tr>

            <!-- Sección de Firmas -->
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

        <!-- Imágenes Asociadas -->
        <h2>Imágenes Asociadas</h2>
        <div class="image-container">
            <?php if ($resultado_imagenes->num_rows > 0): ?>
                <?php while ($img_row = $resultado_imagenes->fetch_assoc()): ?>
                    <div class="image-box" id="image-box-<?php echo $img_row['id']; ?>">
                        <img src="/uploads/<?php echo basename($img_row['imagen']); ?>" alt="Imagen asociada">
                        <span class="delete-btn" data-image-id="<?php echo $img_row['id']; ?>">X</span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay imágenes asociadas a este formulario.</p>
            <?php endif; ?>
        </div>

       <!-- Subir nuevas imágenes (AJAX secuencial, sin submit del formulario) -->
<div class="mb-4 custom-file-upload">
    <label class="custom-label">Subir nuevas imágenes</label>

    <div id="drop-zone">
        <i class="fas fa-cloud-upload-alt drop-icon"></i>
        <div class="drop-title">Arrastra las imágenes aquí</div>
        <div class="drop-sub">o haz clic para seleccionar archivos</div>
        <span class="drop-count" id="drop-count" style="display:none;"></span>
    </div>
    <input type="file" id="nuevas_imagenes" multiple accept="image/*" style="display:none;">

    <div id="upload-queue" class="mt-3" style="display:none;">
        <div id="upload-items"></div>
        <div class="mt-2 d-flex align-items-center" style="gap:12px;">
            <button type="button" id="btn-upload-images" class="btn btn-success">
                <i class="fas fa-upload"></i> Subir imágenes
            </button>
            <span id="upload-summary" style="font-size:0.9rem;color:#555;"></span>
        </div>
    </div>
</div>


<!-- Botón de envío con estilos personalizados -->
<div class="button-container">
    <input class="custom-button custom-submit-button" type="submit" value="Actualizar">
<button type="button" class="custom-button custom-cancel-button" onclick="window.history.back();">Cancelar</button>



</div>


    </form>
	<!-- Modal de Confirmación -->
<div class="modal-backdrop-custom" id="modalBackdrop"></div>

<div class="modal-custom" id="confirmDeleteModal">
    <div class="modal-header-custom">
        <h5 class="modal-title-custom" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
        <button type="button" class="close-custom" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body-custom">
        ¿Estás seguro de que deseas eliminar esta imagen?
    </div>
    <div class="modal-footer-custom">
        <button type="button" class="btn btn-secondary-custom" onclick="closeModal()">Cancelar</button>
        <button type="button" id="confirmDeleteButton" class="btn btn-danger-custom">Eliminar</button>
    </div>
</div>



    <?php endif; // Cierre de if (!$formulario_aprobado) ?>
    <?php
} // Cierre de else (tiene permiso)
?>

<script>
$(document).ready(function() {
    var imageIdToDelete = null;

    // Código JavaScript para eliminar imágenes
    $('.delete-btn').on('click', function() {
        // Obtener el ID de la imagen desde el botón clicado
        imageIdToDelete = $(this).data('image-id');
        // Mostrar el modal de confirmación
        $('#confirmDeleteModal').modal('show');
    });

    // Acciones al confirmar la eliminación en el modal
    $('#confirmDeleteButton').on('click', function() {
        if (imageIdToDelete) {
            var imageBox = $('#image-box-' + imageIdToDelete);

            $.ajax({
                url: 'delete_image_ajax.php',
                type: 'POST',
                data: { image_id: imageIdToDelete },
                success: function(response) {
                    if (response.trim() == 'success') {
                        imageBox.remove();
                        $('#confirmDeleteModal').modal('hide');
                    } else {
                        alert('Error al eliminar la imagen');
                    }
                },
                error: function() {
                    alert('Error de conexión. No se pudo eliminar la imagen.');
                }
            });
        }
    });

    // ----- Subida secuencial de imágenes por AJAX -----
    var filesToUpload = [];
    var formularioId  = <?php echo intval($row['id']); ?>;
    var uploadRunning = false;

    var dropZone = document.getElementById('drop-zone');
    var fileInput = document.getElementById('nuevas_imagenes');

    // Clic en la zona abre el selector
    dropZone.addEventListener('click', function() { fileInput.click(); });

    // Selección por input nativo
    fileInput.addEventListener('change', function() {
        setFiles(Array.from(fileInput.files));
    });

    // Drag & drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });
    dropZone.addEventListener('dragleave', function(e) {
        if (!dropZone.contains(e.relatedTarget)) {
            dropZone.classList.remove('drag-over');
        }
    });
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        var dropped = Array.from(e.dataTransfer.files).filter(function(f) {
            return f.type.startsWith('image/');
        });
        setFiles(dropped);
    });

    function setFiles(files) {
        filesToUpload = files;
        var countEl = document.getElementById('drop-count');
        if (files.length > 0) {
            countEl.textContent = files.length + ' imagen' + (files.length > 1 ? 'es seleccionadas' : ' seleccionada');
            countEl.style.display = 'inline-block';
        } else {
            countEl.style.display = 'none';
        }
        renderUploadQueue();
    }

    function renderUploadQueue() {
        var container = document.getElementById('upload-items');
        container.innerHTML = '';
        document.getElementById('upload-summary').textContent = '';

        if (filesToUpload.length === 0) {
            document.getElementById('upload-queue').style.display = 'none';
            return;
        }

        document.getElementById('upload-queue').style.display = 'block';
        document.getElementById('btn-upload-images').disabled = false;

        filesToUpload.forEach(function(file, index) {
            var item = document.createElement('div');
            item.id = 'upload-item-' + index;
            item.style.cssText = 'display:flex;align-items:center;gap:12px;padding:8px 10px;margin-bottom:6px;border:1px solid #dee2e6;border-radius:8px;background:#fafafa;';
            item.innerHTML =
                '<img id="thumb-' + index + '" style="width:52px;height:52px;object-fit:cover;border-radius:6px;border:1px solid #ddd;" src="">' +
                '<div style="flex:1;min-width:0;">' +
                    '<div style="font-size:0.82rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + escHtml(file.name) + '</div>' +
                    '<div style="font-size:0.75rem;color:#888;margin-bottom:4px;">' + formatBytes(file.size) + '</div>' +
                    '<div style="background:#e9ecef;border-radius:4px;height:7px;overflow:hidden;">' +
                        '<div id="progress-' + index + '" style="width:0%;height:100%;background:#17a2b8;transition:width 0.2s;border-radius:4px;"></div>' +
                    '</div>' +
                '</div>' +
                '<div id="status-' + index + '" style="min-width:90px;text-align:right;">' +
                    '<span style="font-size:0.78rem;padding:3px 8px;border-radius:10px;background:#6c757d;color:#fff;">Pendiente</span>' +
                '</div>';
            container.appendChild(item);

            // Miniatura
            (function(i, f) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var el = document.getElementById('thumb-' + i);
                    if (el) el.src = e.target.result;
                };
                reader.readAsDataURL(f);
            })(index, file);
        });
    }

    // Bloquear recarga/cierre mientras se suben imágenes
    window.addEventListener('beforeunload', function(e) {
        if (uploadRunning) {
            var msg = 'Se están subiendo imágenes. Si sale ahora, perderá todo el trabajo. ¿Desea salir de todas formas?';
            e.preventDefault();
            e.returnValue = msg;
            return msg;
        }
    });

    document.getElementById('btn-upload-images').addEventListener('click', function() {
        if (filesToUpload.length === 0 || uploadRunning) return;
        uploadRunning = true;
        document.getElementById('btn-upload-images').disabled = true;
        document.querySelector('input[type="submit"]').disabled = true;
        document.getElementById('upload-summary').textContent = '';
        uploadNext(0, 0, 0);
    });

    function uploadNext(index, ok, fail) {
        if (index >= filesToUpload.length) {
            uploadRunning = false;
            document.querySelector('input[type="submit"]').disabled = false;

            var summaryEl = document.getElementById('upload-summary');
            if (fail === 0) {
                // Éxito total: mostrar mensaje verde y limpiar cola
                summaryEl.innerHTML =
                    '<div style="margin-top:12px;padding:12px 16px;background:#d4edda;border:1px solid #c3e6cb;border-radius:8px;color:#155724;">' +
                    '<strong>✓ ' + ok + ' imagen' + (ok !== 1 ? 'es subidas' : ' subida') + ' correctamente.</strong><br>' +
                    '<span style="font-size:0.9rem;">Ya puede actualizar el formulario con el botón <em>Actualizar</em>.</span>' +
                    '</div>';
                // Limpiar selección para evitar resubida accidental (items visibles quedan)
                filesToUpload = [];
                fileInput.value = '';
                document.getElementById('drop-count').style.display = 'none';
            } else {
                // Hubo errores: mantener cola visible para reintentar los fallidos
                summaryEl.innerHTML =
                    '<div style="margin-top:12px;padding:12px 16px;background:#fff3cd;border:1px solid #ffeeba;border-radius:8px;color:#856404;">' +
                    '<strong>' + ok + ' subida(s) correctamente</strong>, <strong>' + fail + ' con error</strong>.<br>' +
                    '<span style="font-size:0.9rem;">Revise los errores. Puede volver a intentar subir los archivos.</span>' +
                    '</div>';
                document.getElementById('btn-upload-images').disabled = false;
            }
            return;
        }

        setStatus(index, 'Subiendo...', '#17a2b8');
        setProgress(index, 10, '#17a2b8');

        var formData = new FormData();
        formData.append('formulario_id', formularioId);
        formData.append('imagen', filesToUpload[index]);

        var xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                setProgress(index, Math.round((e.loaded / e.total) * 100), '#17a2b8');
            }
        });

        xhr.addEventListener('load', function() {
            try {
                var resp = JSON.parse(xhr.responseText);
                if (resp.success) {
                    setProgress(index, 100, '#28a745');
                    setStatus(index, 'Subida', '#28a745');
                    uploadNext(index + 1, ok + 1, fail);
                } else {
                    setProgress(index, 100, '#dc3545');
                    setStatus(index, 'Error', '#dc3545', resp.error || '');
                    uploadNext(index + 1, ok, fail + 1);
                }
            } catch(e) {
                setProgress(index, 100, '#dc3545');
                setStatus(index, 'Error', '#dc3545');
                uploadNext(index + 1, ok, fail + 1);
            }
        });

        xhr.addEventListener('error', function() {
            setProgress(index, 100, '#dc3545');
            setStatus(index, 'Sin conexión', '#dc3545');
            uploadNext(index + 1, ok, fail + 1);
        });

        xhr.open('POST', 'upload_image_ajax.php');
        xhr.send(formData);
    }

    function setProgress(index, pct, color) {
        var el = document.getElementById('progress-' + index);
        if (el) { el.style.width = pct + '%'; el.style.background = color; }
    }

    function setStatus(index, text, color, title) {
        var el = document.getElementById('status-' + index);
        if (el) {
            el.innerHTML = '<span title="' + escHtml(title || '') + '" style="font-size:0.78rem;padding:3px 8px;border-radius:10px;background:' + color + ';color:#fff;">' + escHtml(text) + '</span>';
        }
    }

    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function escHtml(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
});
	
	
	
</script>
	
	<script>
	function openModal() {
    var modalBackdrop = document.getElementById('modalBackdrop');
    var modal = document.getElementById('confirmDeleteModal');

    modalBackdrop.style.display = 'block';
    modal.style.display = 'block';

    // Forzar el reflow para asegurarse de que la animación ocurra
    void modal.offsetWidth;

    // Añadir clase "show" para la animación
    modal.classList.add('show');
}

function closeModal() {
    var modalBackdrop = document.getElementById('modalBackdrop');
    var modal = document.getElementById('confirmDeleteModal');

    // Remover la clase "show" para animación de cierre
    modal.classList.remove('show');

    // Esperar el final de la transición antes de ocultar completamente el modal
    setTimeout(function() {
        modalBackdrop.style.display = 'none';
        modal.style.display = 'none';
    }, 500);
}

document.addEventListener('DOMContentLoaded', function () {
    // Evento para abrir el modal
    document.querySelectorAll('.delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openModal();
        });
    });

    // Evento para confirmar la eliminación en el modal
    document.getElementById('confirmDeleteButton').addEventListener('click', function () {
        // Aquí puedes añadir el código de eliminación de la imagen
        closeModal();
        
    });
});


	
	
	</script>


<?php include('../inc/menu-foot.php');?>
<?php include('../inc/footer.php');?>
</body>
</html>

<?php
$conexion->close();
?>




