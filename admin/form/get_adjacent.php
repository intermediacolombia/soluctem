<?php
session_start();
include('../inc/config.php');

header('Content-Type: application/json');

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

$currentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($currentId <= 0) {
    echo json_encode(['prevId' => null, 'nextId' => null]);
    exit;
}

// Zonas del usuario
$zonas_array = array_map('trim', explode(',', $_SESSION['zonas']));
$zonas_escapadas = [];
foreach ($zonas_array as $zona) {
    $zonas_escapadas[] = "'" . $conexion->real_escape_string($zona) . "'";
}
$zonas_para_sql = implode(',', $zonas_escapadas);

$whereBase = "departamento IN ($zonas_para_sql) AND borrado = 0 AND (nombreSolicitante IS NOT NULL AND nombreSolicitante != '')";

// Mismos filtros que get_formularios.php
$customFilters = [];

if (!empty($_GET['filterFechaInicio'])) {
    $customFilters[] = "fecha >= '" . $conexion->real_escape_string($_GET['filterFechaInicio']) . "'";
}
if (!empty($_GET['filterFechaFin'])) {
    $customFilters[] = "fecha <= '" . $conexion->real_escape_string($_GET['filterFechaFin']) . "'";
}
if (!empty($_GET['filterTienda'])) {
    $customFilters[] = "nombreTienda = '" . $conexion->real_escape_string($_GET['filterTienda']) . "'";
}
if (!empty($_GET['filterTecnico'])) {
    $customFilters[] = "nombreSolicitante = '" . $conexion->real_escape_string($_GET['filterTecnico']) . "'";
}
if (isset($_GET['filterEstado']) && $_GET['filterEstado'] !== '') {
    $customFilters[] = "estado = " . intval($_GET['filterEstado']);
}

$whereSearch = "";
if (!empty($_GET['search'])) {
    $s = $conexion->real_escape_string($_GET['search']);
    $whereSearch = " AND (id LIKE '%$s%' OR nombreSolicitante LIKE '%$s%' OR nombreTienda LIKE '%$s%' OR numeroTicket LIKE '%$s%' OR fecha LIKE '%$s%' OR departamento LIKE '%$s%')";
}

$whereCustom = !empty($customFilters) ? " AND " . implode(' AND ', $customFilters) : "";
$whereClause = $whereBase . $whereCustom . $whereSearch;

// Orden por defecto: id DESC
// "Anterior" = ID mayor (fila de arriba en la lista)
// "Siguiente" = ID menor (fila de abajo en la lista)
$prevId = null;
$nextId = null;

$rPrev = $conexion->query("SELECT id FROM formulario WHERE $whereClause AND id > $currentId ORDER BY id ASC LIMIT 1");
if ($rPrev && $rPrev->num_rows > 0) {
    $prevId = intval($rPrev->fetch_assoc()['id']);
}

$rNext = $conexion->query("SELECT id FROM formulario WHERE $whereClause AND id < $currentId ORDER BY id DESC LIMIT 1");
if ($rNext && $rNext->num_rows > 0) {
    $nextId = intval($rNext->fetch_assoc()['id']);
}

$conexion->close();
echo json_encode(['prevId' => $prevId, 'nextId' => $nextId]);
?>
