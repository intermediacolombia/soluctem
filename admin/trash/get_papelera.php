<?php
session_start();
include('../inc/config.php');

// Verificar que sea administrador
if ($_SESSION['rol'] !== 'Administrador') {
    echo json_encode([
        'draw' => 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'No autorizado'
    ]);
    exit;
}

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    echo json_encode([
        'draw' => 0,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Error de conexión'
    ]);
    exit;
}

$conexion->set_charset("utf8");

// Parámetros DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';

// IMPORTANTE: Agregar las nuevas columnas al array
$columns = ['id', 'nombreSolicitante', 'nombreTienda', 'numeroTicket', 'fecha', 'departamento', 'fecha_eliminacion', 'usuario_eliminacion', 'estado'];
$orderColumn = $columns[$orderColumnIndex];
$orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

// WHERE base - Solo borrados (borrado = 1)
$whereBase = "borrado = 1 AND (nombreSolicitante IS NOT NULL AND nombreSolicitante != '')";

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

// Búsqueda
$whereSearch = "";
if (!empty($searchValue)) {
    $searchValue = $conexion->real_escape_string($searchValue);
    $whereSearch = " AND (
        id LIKE '%$searchValue%' OR
        nombreSolicitante LIKE '%$searchValue%' OR
        nombreTienda LIKE '%$searchValue%' OR
        numeroTicket LIKE '%$searchValue%' OR
        fecha LIKE '%$searchValue%' OR
        departamento LIKE '%$searchValue%'
    )";
}

$whereClause = $whereBase . $whereCustom . $whereSearch;

// Contar totales
$sqlTotal = "SELECT COUNT(*) as total FROM formulario WHERE $whereBase";
$resultTotal = $conexion->query($sqlTotal);
$totalRecords = $resultTotal->fetch_assoc()['total'];

$sqlFiltered = "SELECT COUNT(*) as total FROM formulario WHERE $whereClause";
$resultFiltered = $conexion->query($sqlFiltered);
$filteredRecords = $resultFiltered->fetch_assoc()['total'];

// IMPORTANTE: Agregar fecha_eliminacion y usuario_eliminacion al SELECT
$sql = "SELECT id, nombreSolicitante, nombreTienda, numeroTicket, fecha, departamento, estado,
        fecha_eliminacion, usuario_eliminacion 
        FROM formulario 
        WHERE $whereClause 
        ORDER BY $orderColumn $orderDir 
        LIMIT $start, $length";

$result = $conexion->query($sql);

$data = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            'id' => $row['id'],
            'nombreSolicitante' => htmlspecialchars($row['nombreSolicitante']),
            'nombreTienda' => htmlspecialchars($row['nombreTienda']),
            'numeroTicket' => htmlspecialchars($row['numeroTicket']),
            'fecha' => htmlspecialchars($row['fecha']),
            'departamento' => htmlspecialchars($row['departamento']),
            'fecha_eliminacion' => $row['fecha_eliminacion'], // Nueva columna
            'usuario_eliminacion' => $row['usuario_eliminacion'], // Nueva columna
            'estado' => $row['estado']
        );
    }
}

$response = array(
    "draw" => $draw,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($filteredRecords),
    "data" => $data
);

$conexion->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
