<?php
session_start();
include('inc/config.php');

if (!isset($_SESSION['zonas'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

$conexion->set_charset("utf8");

// Obtener zonas del usuario
$zonas_usuario = $_SESSION['zonas'];
$zonas_array = explode(',', $zonas_usuario);
$zonas_array = array_map('trim', $zonas_array);

$zonas_escapadas = array();
foreach ($zonas_array as $zona) {
    $zonas_escapadas[] = "'" . $conexion->real_escape_string($zona) . "'";
}
$zonas_para_sql = implode(',', $zonas_escapadas);

// WHERE base - Solo pendientes
if ($_SESSION['rol'] === 'Administrador') {
    $whereBase = "estado = 0 AND borrado = 0 AND (nombreSolicitante IS NOT NULL AND nombreSolicitante != '')";
} else {
    $whereBase = "estado = 0 AND borrado = 0 AND (nombreSolicitante IS NOT NULL AND nombreSolicitante != '') AND departamento IN ($zonas_para_sql)";
}

$response = array();

// Tiendas
$sqlTiendas = "SELECT DISTINCT nombreTienda 
               FROM formulario 
               WHERE $whereBase 
               AND nombreTienda IS NOT NULL 
               AND nombreTienda != '' 
               ORDER BY nombreTienda ASC";
$resultTiendas = $conexion->query($sqlTiendas);
$tiendas = array();
if ($resultTiendas && $resultTiendas->num_rows > 0) {
    while ($row = $resultTiendas->fetch_assoc()) {
        $tiendas[] = $row['nombreTienda'];
    }
}
$response['tiendas'] = $tiendas;

// Técnicos
$sqlTecnicos = "SELECT DISTINCT nombreSolicitante 
                FROM formulario 
                WHERE $whereBase 
                ORDER BY nombreSolicitante ASC";
$resultTecnicos = $conexion->query($sqlTecnicos);
$tecnicos = array();
if ($resultTecnicos && $resultTecnicos->num_rows > 0) {
    while ($row = $resultTecnicos->fetch_assoc()) {
        $tecnicos[] = $row['nombreSolicitante'];
    }
}
$response['tecnicos'] = $tecnicos;

$conexion->close();

header('Content-Type: application/json');
echo json_encode($response);
?>