<?php
session_start();
include('../inc/config.php');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['zonas'])) {
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

// Establecer charset
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

// Parámetros de DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';

// Mapeo de columnas
$columns = ['id', 'nombreSolicitante', 'nombreTienda', 'numeroTicket', 'fecha', 'departamento', 'estado'];
$orderColumn = $columns[$orderColumnIndex];

// Validar dirección de ordenamiento
$orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

// Construir la condición WHERE base
$whereBase = "departamento IN ($zonas_para_sql) AND borrado = 0 AND (nombreSolicitante IS NOT NULL AND nombreSolicitante != '')";

// Filtros personalizados
$customFilters = [];

// Filtro por fecha inicio
if (!empty($_POST['filterFechaInicio'])) {
    $fechaInicio = $conexion->real_escape_string($_POST['filterFechaInicio']);
    $customFilters[] = "fecha >= '$fechaInicio'";
}

// Filtro por fecha fin
if (!empty($_POST['filterFechaFin'])) {
    $fechaFin = $conexion->real_escape_string($_POST['filterFechaFin']);
    $customFilters[] = "fecha <= '$fechaFin'";
}

// Filtro por tienda
if (!empty($_POST['filterTienda'])) {
    $tienda = $conexion->real_escape_string($_POST['filterTienda']);
    $customFilters[] = "nombreTienda = '$tienda'";
}

// Filtro por técnico
if (!empty($_POST['filterTecnico'])) {
    $tecnico = $conexion->real_escape_string($_POST['filterTecnico']);
    $customFilters[] = "nombreSolicitante = '$tecnico'";
}

// Filtro por estado
if (isset($_POST['filterEstado']) && $_POST['filterEstado'] !== '') {
    $estado = intval($_POST['filterEstado']);
    $customFilters[] = "estado = $estado";
}

// Agregar filtros personalizados
$whereCustom = "";
if (!empty($customFilters)) {
    $whereCustom = " AND " . implode(' AND ', $customFilters);
}

// Agregar búsqueda si existe
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

// Contar total de registros (sin filtros personalizados ni búsqueda)
$sqlTotal = "SELECT COUNT(*) as total FROM formulario WHERE $whereBase";
$resultTotal = $conexion->query($sqlTotal);
$totalRecords = $resultTotal->fetch_assoc()['total'];

// Contar registros filtrados (con todos los filtros)
$sqlFiltered = "SELECT COUNT(*) as total FROM formulario WHERE $whereClause";
$resultFiltered = $conexion->query($sqlFiltered);
$filteredRecords = $resultFiltered->fetch_assoc()['total'];

// Obtener los datos paginados
$sql = "SELECT id, nombreSolicitante, nombreTienda, numeroTicket, fecha, departamento, estado 
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
            'estado' => $row['estado']
        );
    }
}

// Preparar respuesta JSON
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
