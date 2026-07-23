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

    // Consulta SQL para obtener las imágenes asociadas al formulario
    $sql_imagenes = "SELECT * FROM imagenes WHERE formulario_id = $formulario_id";
    $resultado_imagenes = $conexion->query($sql_imagenes);

    if ($resultado_formulario->num_rows > 0) {
        $row = $resultado_formulario->fetch_assoc(); // Obtener el registro

        // Verificar si el formulario está aprobado
        $formulario_aprobado = ($row['estado'] == 1);
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
</head>
<body>
<?php include('../inc/menu.php')?>
	
<h1>Editar Formulario de Mantenimiento</h1>

<form action="update.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="formulario_id" value="<?php echo $row['id']; ?>">
    <?php if ($formulario_aprobado): ?>
        <p style="color: red; font-weight: bold;">Este formulario ya ha sido aprobado y no se puede editar.</p>
    <?php else: ?>
        <!-- Sección de Solicitante -->
    <table>
        <tr class="section-header">
            <td colspan="4"><center>SOLICITANTE Y TIENDA BENEFICIARIA</center></td>
        </tr>
        <tr>
            <th>Nombre del Solicitante</th>
            <td><input type="text" name="nombreSolicitante" value="<?php echo $row['nombreSolicitante']; ?>"></td>
            <th>Cargo</th>
            <td><input type="text" name="cargo" value="<?php echo $row['cargo']; ?>"></td>
        </tr>
        <tr>
            <th>Nombre de la Tienda</th>
            <td><input type="text" name="nombreTienda" value="<?php echo $row['nombreTienda']; ?>"></td>
            <th>Número de Tienda</th>
            <td><input type="text" name="numeroTienda" value="<?php echo $row['numeroTienda']; ?>"></td>
        </tr>
        <tr>
            <th>Número de Ticket</th>
            <td><input type="text" name="numeroTicket" value="<?php echo $row['numeroTicket']; ?>"></td>
            <th>Fecha</th>
            <td><input type="date" name="fecha" value="<?php echo $row['fecha']; ?>"></td>
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
                <select name="municipio" id="municipio" class="form-control" required data-municipio="<?php echo $row['municipio']; ?>">
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
            <td><input type="text" name="nombreEquipo" value="<?php echo $row['nombreEquipo']; ?>"></td>
            <th>Marca</th>
            <td><input type="text" name="marca" value="<?php echo $row['marca']; ?>"></td>
        </tr>
        <tr>
            <th>Serial</th>
            <td colspan="4"><input type="text" name="serial" value="<?php echo $row['serial']; ?>"></td>
        </tr>
        <tr>
            <th>Descripción de la Falla</th>
            <td colspan="4"><textarea name="descripcionFalla"><?php echo $row['descripcionFalla']; ?></textarea></td>
        </tr>
        <tr>
            <th>Diagnóstico Técnico</th>
            <td colspan="3"><textarea name="diagnosticoTecnico"><?php echo $row['diagnosticoTecnico']; ?></textarea></td>
        </tr>
        <tr>
            <th>Repuestos Cambiados</th>
            <td colspan="3"><textarea name="repuestosCambiados"><?php echo $row['repuestosCambiados']; ?></textarea></td>
        </tr>
        <tr>
            <th>Observaciones</th>
            <td colspan="3"><textarea name="observaciones"><?php echo $row['observaciones']; ?></textarea></td>
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
            <td><input type="text" name="contratista1" value="<?php echo $row['contratista1']; ?>"></td>
            <td><input type="text" name="cedula1" value="<?php echo $row['cedula1']; ?>"></td>
            <td><input type="time" name="horaEntrada1" value="<?php echo $row['horaEntrada1']; ?>"></td>
            <td><input type="time" name="horaSalida1" value="<?php echo $row['horaSalida1']; ?>"></td>
        </tr>
        <tr>
            <td><input type="text" name="contratista2" value="<?php echo $row['contratista2']; ?>"></td>
            <td><input type="text" name="cedula2" value="<?php echo $row['cedula2']; ?>"></td>
            <td><input type="time" name="horaEntrada2" value="<?php echo $row['horaEntrada2']; ?>"></td>
            <td><input type="time" name="horaSalida2" value="<?php echo $row['horaSalida2']; ?>"></td>
        </tr>
        <tr>
            <td><input type="text" name="contratista3" value="<?php echo $row['contratista3']; ?>"></td>
            <td><input type="text" name="cedula3" value="<?php echo $row['cedula3']; ?>"></td>
            <td><input type="time" name="horaEntrada3" value="<?php echo $row['horaEntrada3']; ?>"></td>
            <td><input type="time" name="horaSalida3" value="<?php echo $row['horaSalida3']; ?>"></td>
        </tr>
        <tr>
            <td><input type="text" name="contratista4" value="<?php echo $row['contratista4']; ?>"></td>
            <td><input type="text" name="cedula4" value="<?php echo $row['cedula4']; ?>"></td>
            <td><input type="time" name="horaEntrada4" value="<?php echo $row['horaEntrada4']; ?>"></td>
            <td><input type="time" name="horaSalida4" value="<?php echo $row['horaSalida4']; ?>"></td>
        </tr>

        <!-- Sección de Funcionario -->
        <tr class="section-header">
            <td colspan="4"><center>FUNCIONARIO</center></td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td><input type="text" name="nombreFuncionario" value="<?php echo $row['nombreFuncionario']; ?>"></td>
            <th>Cédula</th>
            <td><input type="text" name="cedulaFuncionario" value="<?php echo $row['cedulaFuncionario']; ?>"></td>
        </tr>
        <tr>
            <th>Cargo</th>
            <td><input type="text" name="cargoFuncionario" value="<?php echo $row['cargoFuncionario']; ?>"></td>
            <th>SAP</th>
            <td><input type="text" name="sapFuncionario" value="<?php echo $row['sapFuncionario']; ?>"></td>
        </tr>

        <!-- Sección de Firmas -->
        <tr class="section-header">
            <td colspan="4"><center>FIRMAS</center></td>
        </tr>
        <tr>
            <th>Firma del Cliente</th>
            <td><img src="<?php echo $row['firma_digital_cliente']; ?>" alt="Firma del Cliente" width="200"></td>
            <th>Firma del Técnico</th>
            <td><img src="<?php echo $row['firma_digital_tecnico']; ?>" alt="Firma del Técnico" width="200"></td>
        </tr>
    </table>

    <!-- Imágenes Asociadas -->
    <h2>Imágenes Asociadas</h2>
    <div class="image-container">
        <?php while ($img_row = $resultado_imagenes->fetch_assoc()): ?>
            <div class="image-box" id="image-box-<?php echo $img_row['id']; ?>">
                <img src="/uploads/<?php echo basename($img_row['imagen']); ?>" alt="Imagen asociada">
                <span class="delete-btn" data-image-id="<?php echo $img_row['id']; ?>">X</span>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Subir nuevas imágenes -->
    <h3>Subir nuevas imágenes</h3>
    <input type="file" id="nuevas_imagenes" name="nuevas_imagenes[]" multiple accept="image/*">
    <div id="preview"></div>

    <br><br>
    <input type="submit" value="Actualizar">
</form>
    <?php endif; ?>

    

<script>
$(document).ready(function() {
    $('.delete-btn').on('click', function() {
        var imageId = $(this).data('image-id');
        var imageBox = $('#image-box-' + imageId);

        if (confirm('¿Estás seguro de que deseas eliminar esta imagen?')) {
            $.ajax({
                url: 'delete_image_ajax.php',
                type: 'POST',
                data: { image_id: imageId },
                success: function(response) {
                    if (response.trim() == 'success') {
                        imageBox.remove();
                    } else {
                        alert('Error al eliminar la imagen');
                    }
                }
            });
        }
    });
});

document.getElementById('nuevas_imagenes').addEventListener('change', function(event) {
    var previewContainer = document.getElementById('preview');
    previewContainer.innerHTML = '';

    var files = event.target.files;

    if (files.length > 0) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];

            if (file.type.startsWith('image/')) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var imgElement = document.createElement('img');
                    imgElement.src = e.target.result;
                    imgElement.style.maxWidth = '150px';
                    imgElement.style.margin = '10px';
                    previewContainer.appendChild(imgElement);
                }

                reader.readAsDataURL(file);
            }
        }
    }
});
</script>

<?php include('../inc/menu-foot.php');?>
<?php include('../inc/footer.php');?>
</body>
</html>

<?php
$conexion->close();
?>



