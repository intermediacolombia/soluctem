
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
    
    <title>Formato Único de Soporte - Jerónimo Martins Colombia</title>
    
	<style>
		
		
		body {
    font-family: Arial, sans-serif;
    margin: 12px;
    text-align: center; /* Centrar el contenido del body */
}
		
		.content{
			border: 1px #000 solid;
		}

h1, h2 {
    text-align: center;
    margin: 5px 0;
}

table {
    width: 100%; /* Ocupa el 100% del contenedor */
    max-width: 100%; /* No excede el 100% del ancho disponible */
    border-collapse: collapse;
    
    font-size: 8px;
}

		
		
td, th {
    border: 1px solid #000;
    padding: 2px;
    text-align: left;
    word-wrap: break-word; /* Evitar desbordamiento de texto */
	width: 100%;
}

th {
    background-color: #BEBEBE;
    font-weight: bold;
	text-align: center;
}

td.no-border-right {
    border-right: none; /* Elimina el borde derecho */
}

td.no-border-left {
    border-left: none; /* Elimina el borde izquierdo */
}
		
.image-container {
    margin-top: 10px;
}

.image-container img {
    width: 30%;
    height: auto;
    /*display: block;*/
    margin-bottom: 10px;
    page-break-inside: avoid;
}

.image-box {
    display: block;
    position: relative;
    width: 100%;
}

/* Ajustes para pantallas pequeñas */
@media (max-width: 600px) {
    table {
        font-size: 8px; /* Reducir tamaño de la fuente en pantallas pequeñas */
    }

    td, th {
        padding: 1px; /* Reducir padding en pantallas pequeñas */
    }

    img {
        max-width: 100px; /* Limitar el tamaño de las imágenes */
        height: auto;
    }
}
	
 


    
		
 
</style>


   <body>

	   <?php if ($resultado_formulario->num_rows > 0): ?>
    <?php while ($row = $resultado_formulario->fetch_assoc()): ?>
<center>
	<div class="content">
	<div style="overflow-x:auto;">
<table border="1" cellpadding="0" cellspacing="0">
   

    <tr>
    <!-- Imagen alineada a la izquierda -->
    <td colspan="4" style="border-right: none; text-align: left;">
        <img src="<?php echo $url_sys; ?>/admin/pdf/images/ara.png?<?php echo time(); ?>" width="100px">
    </td>

    <!-- Texto centrado en el medio -->
    <td colspan="7" style="border-left: none; border-right: none; text-align: center;">
        <strong>
            <div style="font-size: 15px;">JERÓNIMO MARTINS COLOMBIA</div>
            FORMATO ÚNICO DE SOPORTE<br>FF-JMC-DT-06
        </strong>
    </td>

    <!-- Imagen alineada a la derecha -->
    <td colspan="4" style="border-left: none; text-align: right;">
        <img src="<?php echo $url_sys; ?>/admin/pdf/images/jeronimo-martins.png?<?php echo time(); ?>" width="200px">
    </td>
</tr>

	
    <tr>
        <th colspan="15">CONTRATISTA</th>
    </tr>
   
    <tr>
        <td colspan="4">Razón social</td>
        <td colspan="5"><strong><?= $razon_social ?></strong></td>
        <td colspan="2">N° NIT:</td>
        <td colspan="4"><strong><?= $nit ?></strong></td>
    </tr>
    <tr>
        <td colspan="4">Contacto:</td>
        <td colspan="5"><strong><?= $contacto ?></strong></td>
        <td colspan="2">Teléfono:</td>
        <td colspan="4"><strong><?= $telefono ?></strong></td>
    </tr>
    <tr>
        <th colspan="15">SOLICITANTE Y TIENDA BENEFICIARIA</th>
    </tr>
  
    <tr>
        <td colspan="3"><strong>Nombre del solictante</strong></td>
        <td colspan="6"><?php echo $row['nombreSolicitante']; ?></td>
        <td><strong>Cargo</strong></td>
        <td colspan="5"><?php echo $row['cargo']; ?></td>
    </tr>
    <tr>
        <td colspan="3"><strong>Nombre de la tienda</strong></td>
        <td colspan="2"><?php echo $row['nombreTienda']; ?></td>
        <td colspan="2"><strong>Nº Tienda</strong></td>
        <td><?php echo $row['numeroTienda']; ?></td>
        <td style="background: red; color: white;">N° TICKET</td>
        <td colspan="2"><?php echo $row['numeroTicket']; ?></td>
        <td colspan="2" rowspan="2"><strong>Fecha</strong></td>
        <td colspan="2" rowspan="2"><?php echo $row['fecha']; ?></td>
        
        
    </tr>
    <tr>
        <td colspan="2"><strong>Municipio</strong></td>
        <td colspan="2"><?php echo $row['municipio']; ?></td>
        <td colspan="3"><strong>Departamento</strong></td>
        <td colspan="4"><?php echo $row['departamento']; ?></td>
    </tr>
    
    <tr>
        <th colspan="15">INFORMACION ÁREA TÉCNICA</th>
    </tr>
    <tr>
        <td colspan="2"><strong>Nombre del equipo</strong></td>
        <td colspan="3"><?php echo $row['nombreEquipo']; ?></td>
        <td colspan="2"><strong>Marca</strong></td>
        <td colspan="4"><?php echo $row['marca']; ?></td>
        <td><strong>Serial</strong></td>
        <td colspan="3"><?php echo $row['serial']; ?></td>
    </tr>
    <tr>
        <th colspan="16">TIPO DE ASISTENCIA</th>
    </tr>
    <tr>
    <td colspan="1">Reparación</td>
    <td><?php echo ($row['asistenciaReparacion'] == 1) ? 'X' : ''; ?></td>
    <td colspan="1">Garantía</td>
    <td><?php echo ($row['asistenciaGarantia'] == 1) ? 'X' : ''; ?></td>
    <td colspan="1">Ajuste</td>
    <td><?php echo ($row['asistenciaAjuste'] == 1) ? 'X' : ''; ?></td>
    <td colspan="1">Modificación</td>
    <td><?php echo ($row['asistenciaModificacion'] == 1) ? 'X' : ''; ?></td>
    <td colspan="1">Servicio</td>
    <td><?php echo ($row['asistenciaServicio'] == 1) ? 'X' : ''; ?></td>
    <td colspan="1">Mejora</td>
    <td><?php echo ($row['asistenciaMejora'] == 1) ? 'X' : ''; ?></td>
    <td colspan="2">Combinación</td>
    <td><?php echo ($row['asistenciaCombinacion'] == 1) ? 'X' : ''; ?></td>
</tr>

   
    <tr>
        <th colspan="15">TIPO/CAUSA DE FALLAS BASICAS</th>
    </tr>
    <tr>
        <td>Operación</td>
        <td><?php echo ($row['fallaOperacion'] == 1) ? 'X' : ''; ?></td>
        <td colspan="2">Mecánica</td>
        <td><?php echo ($row['fallaMecanica'] == 1) ? 'X' : ''; ?></td>
        <td>Eléctrica</td>
        <td><?php echo ($row['fallaElectrica'] == 1) ? 'X' : ''; ?></td>
        <td colspan="2">Daño por terceros</td>
        <td><?php echo ($row['fallaTerceros'] == 1) ? 'X' : ''; ?></td>
        <td colspan="2">Fabricación/Instalación</td>
        <td colspan="3"><?php echo ($row['fallaFabricacion'] == 1) ? 'X' : ''; ?></td>
        
    </tr>
   
    <tr>
        <td colspan="15"><strong>Descripción de la falla funcionario tienda:</strong> <?php echo $row['descripcionFalla']; ?></td>
    </tr>
 
    <tr>
        <td colspan="15"><strong>Diagnóstico del técnico:</strong> <?php echo $row['diagnosticoTecnico']; ?></td>
    </tr>
 
    <tr>
        <td colspan="15"><strong>Repuestos cambiados:</strong> <?php echo $row['repuestosCambiados']; ?></td>
    </tr>
    
    <tr>
        <td colspan="15"><strong>Observaciones:</strong> <?php echo $row['observaciones']; ?></td>
    </tr>
    
    <tr>
        <th colspan="15">EVALUACIÓN DEL SERVICIO</th>
    </tr>
   
    <tr>
        <td colspan="13"><center>PARÁMETROS DE EVALUACIÓN</center></td>
        <td>CUMPLE</td>
        <td>NO CUMPLE</td>
    </tr>
    <tr>
        <td colspan="2" rowspan="2">SEGURIDAD</td>
        <td colspan="11">La labor realizada genera una alta riesgo de accidentalidad para los clientes y/o colaboradores (de ser asi marque no cumple)</td>
        <td><?php
    if ($row['seguridadRiesgoAccidentalidad'] == 'Cumple') {
        echo 'X'; // Muestra una "X" si es Cumple
    } else {
        echo ''; // Deja vacío si es No Cumple
    }
?>
</td>
        <td><?php
    if ($row['seguridadRiesgoAccidentalidad'] == 'No Cumple') {
        echo 'X'; // Muestra una "X" si es No Cumple
    } else {
        echo ''; // Deja vacío si es Cumple
    }
?>
</td>
    </tr>
    <tr>
        <td colspan="11">La labor realizada ofrece algún riesgo para la integridad del equipo (de ser asi marque no cumple)</td>
        <td><?php
    if ($row['seguridadRiesgoEquipo'] == 'Cumple') {
        echo 'X'; // Muestra una "X" si es Cumple
    } else {
        echo ''; // Deja vacío si es No Cumple
    }
?>
</td>
<td><?php
    if ($row['seguridadRiesgoEquipo'] == 'No Cumple') {
        echo 'X'; // Muestra una "X" si es No Cumple
    } else {
        echo ''; // Deja vacío si es Cumple
    }
?>
</td>

    </tr>
    <tr>
        <td colspan="2" rowspan="2">FUNCIONAMIENTO</td>
        <td colspan="11">La falla reportada fue solucionada con el trabajo realizado</td>
       <td><?php
    if ($row['funcionamientoFallaSolucionada'] == 'Cumple') {
        echo 'X'; // Muestra una "X" si es Cumple
    } else {
        echo ''; // Deja vacío si es No Cumple
    }
?>
</td>
<td><?php
    if ($row['funcionamientoFallaSolucionada'] == 'No Cumple') {
        echo 'X'; // Muestra una "X" si es No Cumple
    } else {
        echo ''; // Deja vacío si es Cumple
    }
?>
</td>

    </tr>
    <tr>
        <td colspan="11">Para operar y/o asear el equipo o área intervenida se siguen los pasos normales de manejo anteriores a la asistencia (si debe realizar un procedimiento extra al normal, marque no cumple)</td>
        <td><?php
    if ($row['funcionamientoPasosNormales'] == 'Cumple') {
        echo 'X'; // Muestra una "X" si es Cumple
    } else {
        echo ''; // Deja vacío si es No Cumple
    }
?>
</td>
<td><?php
    if ($row['funcionamientoPasosNormales'] == 'No Cumple') {
        echo 'X'; // Muestra una "X" si es No Cumple
    } else {
        echo ''; // Deja vacío si es Cumple
    }
?>
</td>
    </tr>
    <tr>
        <td colspan="2">CALIDAD</td>
        <td colspan="11">La calidad del trabajo está de acuerdo a la requerida por el personal o el equipo</td>
       <td><?php
    if ($row['calidadTrabajo'] == 'Cumple') {
        echo 'X'; // Muestra una "X" si es Cumple
    } else {
        echo ''; // Deja vacío si es No Cumple
    }
?>
</td>
<td><?php
    if ($row['calidadTrabajo'] == 'No Cumple') {
        echo 'X'; // Muestra una "X" si es No Cumple
    } else {
        echo ''; // Deja vacío si es Cumple
    }
?>
</td>

    </tr>
    <tr>
        <td colspan="2" rowspan="2">LIMPIEZA Y ORGANIZACIÓN</td>
        <td colspan="11">El equipo o área intervenida se dejó armado y/o organizado como se encontraba en un inicio</td>
       <td><?php
    if ($row['limpiezaOrganizacionArmado'] == 'Cumple') {
        echo 'X'; // Muestra una "X" si es Cumple
    } else {
        echo ''; // Deja vacío si es No Cumple
    }
?>
</td>
<td><?php
    if ($row['limpiezaOrganizacionArmado'] == 'No Cumple') {
        echo 'X'; // Muestra una "X" si es No Cumple
    } else {
        echo ''; // Deja vacío si es Cumple
    }
?>
</td>

    </tr>
    <tr>
        <td colspan="11">Los escombros y suciedad generada por el técnico fue aseado</td>
       <td><?php
    if ($row['limpiezaOrganizacionAseado'] == 'Cumple') {
        echo 'X'; // Muestra una "X" si es Cumple
    } else {
        echo ''; // Deja vacío si es No Cumple
    }
?>
</td>
<td><?php
    if ($row['limpiezaOrganizacionAseado'] == 'No Cumple') {
        echo 'X'; // Muestra una "X" si es No Cumple
    } else {
        echo ''; // Deja vacío si es Cumple
    }
?>
</td>

    </tr>
    <tr>
        <td colspan="2" rowspan="3">CAPACITACION</td>
        <td colspan="11">Se indicó la causa de la novedad al personal que recibió el trabajo</td>
        <td><?php
    if ($row['capacitacionCausa'] == 'Cumple') {
        echo 'X'; // Muestra una "X" si es Cumple
    } else {
        echo ''; // Deja vacío si es No Cumple
    }
?>
</td>
<td><?php
    if ($row['capacitacionCausa'] == 'No Cumple') {
        echo 'X'; // Muestra una "X" si es No Cumple
    } else {
        echo ''; // Deja vacío si es Cumple
    }
?>
</td>

    </tr>
    <tr>
        <td colspan="11">Se indicó cómo prevenir que el problema se vuelva a presentar</td>
        <td><?php
    if ($row['capacitacionPrevencion'] == 'Cumple') {
        echo 'X'; // Muestra una "X" si es Cumple
    } else {
        echo ''; // Deja vacío si es No Cumple
    }
?>
</td>
<td><?php
    if ($row['capacitacionPrevencion'] == 'No Cumple') {
        echo 'X'; // Muestra una "X" si es No Cumple
    } else {
        echo ''; // Deja vacío si es Cumple
    }
?>
</td>

    </tr>
    <tr>
        <td colspan="11">Se indicó cómo actuar en caso de que el problema se vuelva a presentar</td>
       <td><?php
    if ($row['capacitacionAccion'] == 'Cumple') {
        echo 'X'; // Muestra una "X" si es Cumple
    } else {
        echo ''; // Deja vacío si es No Cumple
    }
?>
</td>
<td><?php
    if ($row['capacitacionAccion'] == 'No Cumple') {
        echo 'X'; // Muestra una "X" si es No Cumple
    } else {
        echo ''; // Deja vacío si es Cumple
    }
?>
</td>

    </tr>
  
    <tr>
        <th colspan="15">CONSTANCIA REALIZACIÓN ASISTENCIA</th>
    </tr>
  
    <tr>
        <td colspan="4"><strong>Contratistas</strong></td>
        <td><strong>Cédula</strong></td>
        <td colspan="2"><strong>Hora de entrada</strong></td>
        <td colspan="2"><strong>Hora de salida</strong></td>
        <td><strong>Datos</strong></td>
        <td colspan="6"><strong>Funcionario de la tienda</strong></td>
    </tr>
    <tr>
        <td colspan="4"><?php echo $row['contratista1'] ?: ''; ?></td>
<td><?php echo $row['cedula1'] ?: ''; ?></td>
<td colspan="2"><?php echo ($row['horaEntrada1'] != '00:00:00') ? $row['horaEntrada1'] : ''; ?></td>
<td colspan="2"><?php echo ($row['horaSalida1'] != '00:00:00') ? $row['horaSalida1'] : ''; ?></td>
<td>Nombre:</td>
<td colspan="5"><?php echo $row['nombreFuncionario']; ?></td>
</tr>
<tr>
<td colspan="4"><?php echo $row['contratista2'] ?: ''; ?></td>
<td><?php echo $row['cedula2'] ?: ''; ?></td>
<td colspan="2"><?php echo ($row['horaEntrada2'] != '00:00:00') ? $row['horaEntrada2'] : ''; ?></td>
<td colspan="2"><?php echo ($row['horaSalida2'] != '00:00:00') ? $row['horaSalida2'] : ''; ?></td>
<td>Cédula:</td>
<td colspan="5"><?php echo $row['cedulaFuncionario']; ?></td>
</tr>
<tr>
<td colspan="4"><?php echo $row['contratista3'] ?: ''; ?></td>
<td><?php echo $row['cedula3'] ?: ''; ?></td>
<td colspan="2"><?php echo ($row['horaEntrada3'] != '00:00:00') ? $row['horaEntrada3'] : ''; ?></td>
<td colspan="2"><?php echo ($row['horaSalida3'] != '00:00:00') ? $row['horaSalida3'] : ''; ?></td>
<td>Cargo:</td>
<td colspan="5"><?php echo $row['cargoFuncionario']; ?></td>
</tr>
<tr>
<td colspan="4"><?php echo $row['contratista4'] ?: ''; ?></td>
<td><?php echo $row['cedula4'] ?: ''; ?></td>
<td colspan="2"><?php echo ($row['horaEntrada4'] != '00:00:00') ? $row['horaEntrada4'] : ''; ?></td>
<td colspan="2"><?php echo ($row['horaSalida4'] != '00:00:00') ? $row['horaSalida4'] : ''; ?></td>
<td>SAP:</td>
<td colspan="5"><?php echo $row['sapFuncionario']; ?></td>
</tr>

    <tr>
        <td colspan="2">Firma Técnico Encargado:</td><td colspan="8"><img src="<?php echo $row['firma_digital_tecnico']; ?>" alt="Firma del Técnico" width="150px"></td>
        <td rowspan="2">Firma:</td>
        <td colspan="4" rowspan="2"><img src="<?php echo $row['firma_digital_cliente']; ?>" alt="Firma del Cliente" width="150px"></td>
    </tr>
    <tr>
        <td colspan="2">Cargo:</td>
        <td colspan="8">Tecnico</td>
    </tr>
</table>
</div>
		</div>
	</center>
  <?php endwhile; ?>
	   
	<br>
<br>
<br>

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