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
    // Consulta SQL para obtener los datos del formulario
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
                f.timestamp, 
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
                f.firma_digital_tecnico
            FROM formulario f
            WHERE f.id = $formulario_id";

    $resultado_formulario = $conexion->query($sql_formulario);

    // Consulta SQL para obtener las imágenes asociadas al formulario
    $sql_imagenes = "SELECT imagen FROM imagenes WHERE formulario_id = $formulario_id";
    $resultado_imagenes = $conexion->query($sql_imagenes);

} else {
    die("ID de formulario no válido.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario para Imprimir</title>
	<style>
	@media print {
    body {
        font-size: 9px; /* Ajusta el tamaño de la fuente para la impresión */
        margin: 0; /* Eliminar márgenes en la impresión */
    }
    
    table {
        width: 100%; /* Asegúrate de que la tabla use el 100% del ancho disponible */
        font-size: 9px; /* Reducir el tamaño de la fuente de la tabla para ajustarse mejor a la página */
    }
    
    th, td {
        padding: 4px; /* Reducir el padding para que las celdas ocupen menos espacio */
        word-wrap: break-word; /* Permitir que el contenido dentro de las celdas se ajuste automáticamente */
    }

    .signature img {
        width: 150px; /* Reducir el tamaño de las imágenes en la sección de firma */
        height: 60px; /* Ajustar la altura de las imágenes */
    }

    img {
        max-width: 100%; /* Asegurarse de que las imágenes no se desborden */
        height: auto;
    }

    .image-container img {
        max-width: 100%;
        height: auto;
    }

    .signature-section {
        display: flex;
        justify-content: space-between;
    }

    /* Ocultar elementos que no se quieren imprimir */
    .delete-btn {
        display: none;
    }
}



	</style>
   
</head>
<body>


<center><img src="<?php echo $url_sys; ?>/admin/pdf/images/logo.png" width="250px"></center>
	<br>
	<h1>Mantenimiento Nro <?php echo $formulario_id;?></h1><br>

<?php if ($resultado_formulario->num_rows > 0): ?>
    <?php while ($row = $resultado_formulario->fetch_assoc()): ?>
        <table>
            <tr class="section-header">
                <td colspan="4"><center>SOLICITANTE Y TIENDA BENEFICIARIA</center></td>
            </tr>
            <tr>
                <th>Nombre del Solicitante</th>
                <td><?php echo $row['nombreSolicitante']; ?></td>
                <th>Cargo</th>
                <td><?php echo $row['cargo']; ?></td>
            </tr>
            <tr>
                <th>Nombre de la Tienda</th>
                <td><?php echo $row['nombreTienda']; ?></td>
                <th>Número de Tienda</th>
                <td><?php echo $row['numeroTienda']; ?></td>
            </tr>
            <tr>
                <th>Número de Ticket</th>
                <td><?php echo $row['numeroTicket']; ?></td>
                <th>Fecha</th>
                <td><?php echo $row['fecha']; ?></td>
            </tr>
            <tr>
				<th>Departamento</th>
                <td><?php echo $row['departamento']; ?></td>
                <th>Municipio</th>
                <td><?php echo $row['municipio']; ?></td>
                
            </tr>

			<!-- Sección de Tipo de Asistencia -->
<tr class="section-header">
    <td colspan="4"><center>TIPO DE ASISTENCIA</center></td>
</tr>
<!-- Dividimos los tipos de asistencia en dos filas para que se ajusten dentro de la estructura de 4 columnas -->
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
    <td colspan="2"></td> <!-- Esto llena el espacio en blanco si faltan columnas -->
</tr>

<!-- Sección de Tipo/Causa de Fallas Básicas -->
<tr class="section-header">
    <td colspan="4"><center>TIPO/CAUSA DE FALLAS BÁSICAS</center></td>
</tr>
<!-- Dividimos los tipos de fallas en varias filas para ajustarlas dentro de las 4 columnas -->
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
    <td colspan="2"></td> <!-- Dejar esta celda en blanco para equilibrar el espacio si faltan columnas -->
</tr>



			
            <!-- Información Área Técnica -->
<tr class="section-header">
    <td colspan="4"><center>INFORMACIÓN ÁREA TÉCNICA</center></td>
</tr>
<tr>
    <th>Nombre del Equipo</th>
    <td><?php echo $row['nombreEquipo']; ?></td>
    <th>Marca</th>
    <td><?php echo $row['marca']; ?></td>				
</tr>
<tr>
    <th>Serial</th>
    <td colspan="3"><?php echo $row['serial']; ?></td>
</tr>
<tr>
    <th>Descripción de la Falla</th>
    <td colspan="3"><?php echo $row['descripcionFalla']; ?></td>
</tr>
<tr>
    <th>Diagnóstico Técnico</th>
    <td colspan="3"><?php echo $row['diagnosticoTecnico']; ?></td>
</tr>
<tr>
    <th>Repuestos Cambiados</th>
    <td colspan="3"><?php echo $row['repuestosCambiados']; ?></td>
</tr>
<tr>
    <th>Observaciones</th>
    <td colspan="3"><?php echo $row['observaciones']; ?></td>
</tr>


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
    <td><?php echo $row['seguridadRiesgoAccidentalidad']; ?></td>
</tr>
<tr>
    <td colspan="3">La labor realizada ofrece algún riesgo para la integridad del equipo</td>
    <td><?php echo $row['seguridadRiesgoEquipo']; ?></td>
</tr>
			<tr class="section-header">
    <td colspan="4"><center>FUNCIONALIDAD</center></td>
</tr>
<tr>
    <td colspan="3">La falla reportada fue solucionada con el trabajo realizado</td>
    <td><?php echo $row['funcionamientoFallaSolucionada']; ?></td>
</tr>
<tr>
    <td colspan="3">Los pasos normales de manejo se siguen sin procedimientos extra</td>
    <td><?php echo $row['funcionamientoPasosNormales']; ?></td>
</tr>
<tr class="section-header">
    <td colspan="4"><center>CALIDAD</center></td>
</tr>
<tr>
    <td  colspan="3">La calidad del trabajo fue adecuada</td>
    <td><?php echo $row['calidadTrabajo']; ?></td>
</tr>
			<tr class="section-header">
    <td colspan="4"><center>LIMPIEZA</center></td>
</tr>
<tr>
    <td  colspan="3">Limpieza - El área intervenida fue dejada organizada y aseada</td>
    <td><?php echo $row['limpiezaOrganizacionArmado']; ?></td>
</tr>
<tr>
    <td  colspan="3">Limpieza - Los escombros fueron limpiados</td>
    <td><?php echo $row['limpiezaOrganizacionAseado']; ?></td>
</tr>
			<!-- CAPACITACIÓN -->
<tr class="section-header">
    <td colspan="4"><center>CAPACITACIÓN</center></td>
</tr>
<!-- Evaluaciones faltantes -->
<tr>
    <td colspan="3">Se indico la causa de la novedad al personal que recibió el trabajo</td>
    <td><?php echo $row['capacitacionCausa']; ?></td>
</tr>
<tr>
    <td colspan="3">Se indico como prevenir que el problema se vuelva a presentar</td>
    <td><?php echo $row['capacitacionPrevencion']; ?></td>
</tr>
<tr>
    <td colspan="3">Se indico como actuar en caso de que el problema se vuelva a presentar</td>
    <td><?php echo $row['capacitacionAccion']; ?></td>
</tr>


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
                <td><?php echo $row['contratista1'] ?: ''; ?></td>
                <td><?php echo $row['cedula1'] ?: ''; ?></td>
                <td><?php echo $row['horaEntrada1'] ?: ''; ?></td>
                <td><?php echo $row['horaSalida1'] ?: ''; ?></td>
            </tr>
			<?php if (!empty($row['contratista2'])): ?>
            <tr>
                <td><?php echo $row['contratista2'] ?: ''; ?></td>
                <td><?php echo $row['cedula2'] ?: ''; ?></td>
                <td><?php echo $row['horaEntrada2'] ?: ''; ?></td>
                <td><?php echo $row['horaSalida2'] ?: ''; ?></td>
            </tr>
			<?php endif; ?>
			<?php if (!empty($row['contratista3'])): ?>
            <tr>
                <td><?php echo $row['contratista3'] ?: ''; ?></td>
                <td><?php echo $row['cedula3'] ?: ''; ?></td>
                <td><?php echo $row['horaEntrada3'] ?: ''; ?></td>
                <td><?php echo $row['horaSalida3'] ?: ''; ?></td>
            </tr>
			<?php endif; ?>
			<?php if (!empty($row['contratista4'])): ?>
            <tr>
                <td><?php echo $row['contratista4'] ?: ''; ?></td>
                <td><?php echo $row['cedula4'] ?: ''; ?></td>
                <td><?php echo $row['horaEntrada4'] ?: ''; ?></td>
                <td><?php echo $row['horaSalida4'] ?: ''; ?></td>
            </tr>
			<?php endif; ?>
 <tr class="section-header">
                <td colspan="4"><center>FUNCIONARIO</center></td>
            </tr>
            <tr>
                <th>Nombre</th>
                <td><?php echo $row['nombreFuncionario']; ?></td>
                <th>Cédula</th>
                <td><?php echo $row['cedulaFuncionario']; ?></td>
            </tr>
            <tr>
                <th>Cargo</th>
                <td><?php echo $row['cargoFuncionario']; ?></td>
                <th>SAP</th>
                <td><?php echo $row['sapFuncionario']; ?></td>
            </tr>
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
    <?php endwhile; ?>

    <h2>Fotos</h2>
    <div class="image-container">
        <?php while ($img_row = $resultado_imagenes->fetch_assoc()): ?>
            <img src="<?php echo $url_sys;?><?php echo $img_row['imagen']; ?>" alt="Imagen asociada">
        <?php endwhile; ?>
    </div>

<?php else: ?>
    <p>No se encontraron datos para el ID de formulario proporcionado.</p>
<?php endif; ?>

</body>
</html>

<?php
$conexion->close();
?>

