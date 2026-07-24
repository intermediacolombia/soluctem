<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
include('../admin/inc/config.php');

if ($authHeader !== $api_auth_token) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token de autorizacion no valido.']);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexion: ' . $conn->connect_error]);
    exit;
}

$conn->set_charset('utf8mb4');

function postValue($key) {
    return isset($_POST[$key]) ? $_POST[$key] : '';
}

function cleanValue($conn, $key) {
    $value = postValue($key);
    $value = preg_replace('/[^\P{C}\n\t]/u', '', $value);
    return $conn->real_escape_string($value);
}

try {
    $timestamp = cleanValue($conn, 'timestamp');
    if ($timestamp === '') {
        throw new Exception('Falta el timestamp del formulario.');
    }

    $existing = $conn->query("SELECT * FROM formulario WHERE timestamp = '$timestamp' LIMIT 1");
    if ($existing && $existing->num_rows > 0) {
        $row = $existing->fetch_assoc();
        $existingId = intval($row['id']);
        $conn->query("DELETE FROM imagenes WHERE formulario_id = '$existingId'");

        echo json_encode([
            'success' => true,
            'message' => 'Formulario ya existia en el servidor. Se reiniciara la carga de imagenes.',
            'data' => $row,
            'already_exists' => true
        ]);
        $conn->close();
        exit;
    }

    $fields = [
        'nombreSolicitante', 'cargo', 'nombreTienda', 'numeroTienda', 'numeroTicket', 'fecha', 'municipio', 'departamento',
        'nombreEquipo', 'marca', 'serial', 'descripcionFalla', 'diagnosticoTecnico', 'repuestosCambiados', 'observaciones',
        'seguridadRiesgoAccidentalidad', 'seguridadRiesgoEquipo', 'funcionamientoFallaSolucionada', 'funcionamientoPasosNormales',
        'calidadTrabajo', 'limpiezaOrganizacionArmado', 'limpiezaOrganizacionAseado', 'capacitacionCausa', 'capacitacionPrevencion',
        'capacitacionAccion', 'contratista1', 'cedula1', 'horaEntrada1', 'horaSalida1', 'contratista2', 'cedula2', 'horaEntrada2',
        'horaSalida2', 'contratista3', 'cedula3', 'horaEntrada3', 'horaSalida3', 'contratista4', 'cedula4', 'horaEntrada4',
        'horaSalida4', 'nombreFuncionario', 'cedulaFuncionario', 'cargoFuncionario', 'sapFuncionario', 'timestamp',
        'asistenciaReparacion', 'asistenciaGarantia', 'asistenciaAjuste', 'asistenciaModificacion', 'asistenciaServicio',
        'asistenciaMejora', 'asistenciaCombinacion', 'fallaOperacion', 'fallaMecanica', 'fallaElectrica', 'fallaTerceros',
        'fallaFabricacion', 'firma_digital_cliente', 'firma_digital_tecnico'
    ];

    $columns = implode(', ', $fields);
    $values = [];
    foreach ($fields as $field) {
        $values[] = "'" . cleanValue($conn, $field) . "'";
    }

    $sql = "INSERT INTO formulario ($columns) VALUES (" . implode(', ', $values) . ")";
    if (!$conn->query($sql)) {
        throw new Exception('Error al guardar el formulario: ' . $conn->error);
    }

    $formularioId = $conn->insert_id;
    $result = $conn->query("SELECT * FROM formulario WHERE id = '$formularioId' LIMIT 1");
    if (!$result || $result->num_rows === 0) {
        throw new Exception('No se pudo verificar el registro insertado.');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Formulario enviado exitosamente.',
        'data' => $result->fetch_assoc()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>

