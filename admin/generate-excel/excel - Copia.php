<?php
session_start(); // Asegurarse de iniciar la sesión para usar $_SESSION
include('../inc/config.php');

// Conectar a la base de datos
$conexion = new mysqli($servername, $username, $password, $dbname);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener las fechas del rango
$fecha_inicio = $_GET['fecha_inicio'];
$fecha_fin = $_GET['fecha_fin'];

// Consulta SQL para obtener los datos en el rango de fechas
$sql = "SELECT contratista1, nombreSolicitante, horaEntrada1, horaSalida1, fecha, numeroTienda, nombreTienda, municipio, nombreEquipo, serial, descripcionFalla, diagnosticoTecnico, numeroTicket, observaciones, repuestosCambiados 
        FROM formulario 
        WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' 
        ORDER BY fecha ASC";
$resultado = $conexion->query($sql);

// Verificar si existen registros en el rango de fechas
if ($resultado->num_rows === 0) {
    $_SESSION['no_data'] = "No se encontraron formularios en el rango de fechas seleccionado.";
    header('Location: /admin/generate-excel/'); // Redirigir al formulario de selección de fechas
    exit();
}

// Encabezados para la descarga del archivo Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=reporte_contratistas_" . $fecha_inicio . "_al_" . $fecha_fin . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Añadir el BOM (Byte Order Mark) para que Excel reconozca el archivo en UTF-8
echo "\xEF\xBB\xBF";

// Generar la tabla con los datos en formato HTML para Excel
echo "<table border='1'>
    <thead>
        <tr>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>NOMBRE TÉCNICO</th>
			<th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>NOMBRE SOLICITANTE</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>HORA ENTRADA</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>HORA DE SALIDA</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>FECHA</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>NUMERO DE TIENDA</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>NOMBRE DE TIENDA</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>CIUDAD</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>EQUIPO</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>SAP DE EQUIPO</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>MOTIVO DEL SERVICIO</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>REPORTE TÉCNICO</th>
            <th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>TICKET</th>
			<th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>OBSERVACIONES</th>
			<th style='background-color:#d3d3d3;color:#000000;font-weight:bold;'>REPUESTOS CAMBIADOS</th>
        </tr>
    </thead>
    <tbody>";

// Rellenar los datos de la consulta
while ($row = $resultado->fetch_assoc()) {
    echo "<tr>
            <td>{$row['contratista1']}</td>
			<td>{$row['nombreSolicitante']}</td>
            <td>{$row['horaEntrada1']}</td>
            <td>{$row['horaSalida1']}</td>
            <td>{$row['fecha']}</td>
            <td>{$row['numeroTienda']}</td>
            <td>{$row['nombreTienda']}</td>
            <td>{$row['municipio']}</td>
            <td>{$row['nombreEquipo']}</td>
            <td>{$row['serial']}</td>
            <td>{$row['descripcionFalla']}</td>
            <td>{$row['diagnosticoTecnico']}</td>
            <td>{$row['numeroTicket']}</td>
			<td>{$row['observaciones']}</td>
			<td>{$row['repuestosCambiados']}</td>
        </tr>";
}

echo "
    </tbody>
</table>";

// Cerrar la conexión
$conexion->close();
exit();
?>




