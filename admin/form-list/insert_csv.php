<?php
session_start();
include('../inc/config.php'); // Asegúrate de que este archivo tiene los datos de conexión

// Verifica que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Método no permitido. Usa POST.']));
}

// Verifica que el campo `confirm_insert` esté presente
if (!isset($_POST['confirm_insert'])) {
    die(json_encode(['success' => false, 'message' => 'Acceso no autorizado. Falta confirmación.']));
}

// Verifica que se haya enviado el CSV
if (empty($_POST['csv_data'])) {
    die(json_encode(['success' => false, 'message' => 'No se recibió ningún CSV.']));
}

// Procesar el CSV
// Procesar el CSV de forma segura
$csvData = trim($_POST['csv_data']);
$stream = fopen('php://memory', 'r+');
fwrite($stream, $csvData);
rewind($stream);

$rows = [];
while (($data = fgetcsv($stream, 0, ';')) !== false) {
    $rows[] = $data;
}
fclose($stream);

// Sacar encabezados
$headers = $rows[0];
$headers[0] = preg_replace('/\xEF\xBB\xBF/', '', $headers[0]); // quitar BOM
array_shift($rows);

// Ahora $rows tiene todas las filas bien parseadas



// Encabezados esperados en la BD
$expectedHeaders = [
    'nombreSolicitante', 'cargo', 'nombreTienda', 'numeroTienda', 'numeroTicket', 'fecha', 'municipio', 'departamento',
    'nombreEquipo', 'marca', 'serial', 'descripcionFalla', 'diagnosticoTecnico', 'repuestosCambiados', 'observaciones',
    'seguridadRiesgoAccidentalidad', 'seguridadRiesgoEquipo', 'funcionamientoFallaSolucionada', 'funcionamientoPasosNormales',
    'calidadTrabajo', 'limpiezaOrganizacionArmado', 'limpiezaOrganizacionAseado', 'capacitacionCausa',
    'capacitacionPrevencion', 'capacitacionAccion', 'contratista1', 'cedula1', 'horaEntrada1', 'horaSalida1',
    'contratista2', 'cedula2', 'horaEntrada2', 'horaSalida2', 'contratista3', 'cedula3', 'horaEntrada3', 'horaSalida3',
    'contratista4', 'cedula4', 'horaEntrada4', 'horaSalida4', 'nombreFuncionario', 'cedulaFuncionario', 'cargoFuncionario',
    'sapFuncionario', 'asistenciaReparacion', 'asistenciaGarantia', 'asistenciaAjuste', 'asistenciaModificacion',
    'asistenciaServicio', 'asistenciaMejora', 'asistenciaCombinacion', 'fallaOperacion', 'fallaMecanica',
    'fallaElectrica', 'fallaTerceros', 'fallaFabricacion', 'firma_digital_cliente', 'firma_digital_tecnico'
];

// Verificar que los encabezados coincidan
if ($headers !== $expectedHeaders) {
    die(json_encode(['success' => false, 'message' => 'Error en el encabezado del CSV. Verifica el formato.']));
}

// Conectar a la BD
$conexion = new mysqli($servername, $username, $password, $dbname);
if ($conexion->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conexion->connect_error]));
}

// Preparar la consulta de inserción
$query = "INSERT INTO formulario (" . implode(", ", $expectedHeaders) . ", estado) VALUES (" . str_repeat('?,', count($expectedHeaders)) . " 0)";
$stmt = $conexion->prepare($query);
if (!$stmt) {
    die(json_encode(['success' => false, 'message' => 'Error en la consulta: ' . $conexion->error]));
}

// Iniciar transacción
$conexion->begin_transaction();

try {
    foreach ($rows as $row) {
    if (count($row) !== count($expectedHeaders)) {
        throw new Exception('Error en una fila: cantidad incorrecta de columnas.');
    }

    // Limpiar datos y asignarlos
    $params = array_map(fn($val) => $val === '' ? null : $val, $row);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);

    if (!$stmt->execute()) {
        throw new Exception('Error al insertar fila: ' . $stmt->error);
    }
}


    // Confirmar transacción
    $conexion->commit();
    echo json_encode(['success' => true, 'message' => 'Formulario Importado correctamente.']);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Cerrar conexión
$stmt->close();
$conexion->close();

