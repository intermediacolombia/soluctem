<?php
session_start();
include('../inc/config.php');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['zonas'])) {
    echo json_encode(['total' => 0, 'pendientes' => 0, 'aprobados' => 0, 'error' => 'No autorizado']);
    exit;
}

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    echo json_encode(['total' => 0, 'pendientes' => 0, 'aprobados' => 0, 'error' => 'Error de conexión']);
    exit;
}

$conexion->set_charset("utf8");

// Obtener las zonas del usuario
$zonas_usuario = $_SESSION['zonas'];
$zonas_array = explode(',', $zonas_usuario);
$zonas_array = array_map('trim', $zonas_array);

// Preparar las zonas para la consulta
$zonas_escapadas = array();
foreach ($zonas_array as $zona) {
    $zonas_escapadas[] = "'" . $conexion->real_escape_string($zona) . "'";
}
$zonas_para_sql = implode(',', $zonas_escapadas);

// Construir WHERE base
$whereBase = "departamento IN ($zonas_para_sql) AND borrado = 0 AND (nombreSolicitante IS NOT NULL AND nombreSolicitante != '')";

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

// Obtener estadísticas
$stats = array('total' => 0, 'pendientes' => 0, 'aprobados' => 0);

// Total
$sqlTotal = "SELECT COUNT(*) as total FROM formulario WHERE $whereClause";
$resultTotal = $conexion->query($sqlTotal);
if ($resultTotal) {
    $row = $resultTotal->fetch_assoc();
    $stats['total'] = intval($row['total']);
}

// Pendientes
$sqlPendientes = "SELECT COUNT(*) as total FROM formulario WHERE $whereClause AND estado = 0";
$resultPendientes = $conexion->query($sqlPendientes);
if ($resultPendientes) {
    $row = $resultPendientes->fetch_assoc();
    $stats['pendientes'] = intval($row['total']);
}

// Aprobados
$sqlAprobados = "SELECT COUNT(*) as total FROM formulario WHERE $whereClause AND estado = 1";
$resultAprobados = $conexion->query($sqlAprobados);
if ($resultAprobados) {
    $row = $resultAprobados->fetch_assoc();
    $stats['aprobados'] = intval($row['total']);
}

// Ajustar si hay filtro de estado
if (isset($_POST['filterEstado']) && $_POST['filterEstado'] !== '') {
    $estadoFiltro = intval($_POST['filterEstado']);
    if ($estadoFiltro == 0) {
        $stats['aprobados'] = 0;
    } else if ($estadoFiltro == 1) {
        $stats['pendientes'] = 0;
    }
}

$conexion->close();

header('Content-Type: application/json');
echo json_encode($stats);
?>
