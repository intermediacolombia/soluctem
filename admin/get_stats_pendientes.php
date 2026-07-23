<?php
session_start();
include('inc/config.php');


if (!isset($_SESSION['zonas'])) {
    echo json_encode(['total' => 0, 'hoy' => 0, 'semana' => 0, 'error' => 'No autorizado']);
    exit;
}

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    echo json_encode(['total' => 0, 'hoy' => 0, 'semana' => 0, 'error' => 'Error de conexión']);
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

// Filtros personalizados
$customFilters = [];

if (!empty($_POST['filterFechaInicio'])) {
    $fechaInicio = $conexion->real_escape_string($_POST['filterFechaInicio']);
    $customFilters[] = "fecha >= '$fechaInicio'";
}

if (!empty($_POST['filterFechaFin'])) {
    $fechaFin = $conexion->real_escape_string($_POST['filterFechaFin']);
    $customFilters[] = "fecha <= '$fechaFin'";
}

if (!empty($_POST['filterTienda'])) {
    $tienda = $conexion->real_escape_string($_POST['filterTienda']);
    $customFilters[] = "nombreTienda = '$tienda'";
}

if (!empty($_POST['filterTecnico'])) {
    $tecnico = $conexion->real_escape_string($_POST['filterTecnico']);
    $customFilters[] = "nombreSolicitante = '$tecnico'";
}

$whereCustom = "";
if (!empty($customFilters)) {
    $whereCustom = " AND " . implode(' AND ', $customFilters);
}

$whereClause = $whereBase . $whereCustom;

$stats = array('total' => 0, 'hoy' => 0, 'semana' => 0);

// Total pendientes
$sqlTotal = "SELECT COUNT(*) as total FROM formulario WHERE $whereClause";
$resultTotal = $conexion->query($sqlTotal);
if ($resultTotal) {
    $row = $resultTotal->fetch_assoc();
    $stats['total'] = intval($row['total']);
}

// Pendientes de hoy
$sqlHoy = "SELECT COUNT(*) as total FROM formulario WHERE $whereClause AND DATE(fecha) = '$hoy'";
$resultHoy = $conexion->query($sqlHoy);
if ($resultHoy) {
    $row = $resultHoy->fetch_assoc();
    $stats['hoy'] = intval($row['total']);
}

// Pendientes de esta semana
$sqlSemana = "SELECT COUNT(*) as total FROM formulario WHERE $whereClause AND YEARWEEK(fecha, 1) = YEARWEEK('$hoy', 1)";
$resultSemana = $conexion->query($sqlSemana);
if ($resultSemana) {
    $row = $resultSemana->fetch_assoc();
    $stats['semana'] = intval($row['total']);
}

$conexion->close();

header('Content-Type: application/json');
echo json_encode($stats);
?>