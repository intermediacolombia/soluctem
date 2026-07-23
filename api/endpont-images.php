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
$authToken = '5fuUMw9Km7FypJFdlaHoSkQvDl4ijrPsuTkzYc47od3T23CacsODQOI72830kld8xmcTa2TzQVDeXQXlR2jGOoL4IttJO0zpFSWmaSE03zbZL15Op4kXAdIljfEqwLYOHzKIbtttsJMFeHdI2YW7qr2Fu5pZJTRR1XmiKK4y5oZch8d6Si5XSpbCq9HGxhQMZHyq7L1AcvT5gyTQ9Uk5xYgcjhMIA1ZcAoU88p5LuzQKxbk7KU3HOECBz0jJJcrPf55dKaUY35oQKoSU223wFh0vIpvK7WWtLiOAEZDkM6UQT9rjbWhjHqgsbdWk8BJKziEbp2mFs6G54fd4TDXZiEUz6uXiswtcIljSIauCp9Du3VJiWAv3uyfpRhCZ8l5ZjQIEQtQYcLQK16KxDgFWbK6QvpGsOts81OTQA0UhFtyzs9E2ziRx8LGgQYGTSzB5o2ahg2MIDl2u2C0';

if ($authHeader !== $authToken) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token de autorizacion no valido.']);
    exit;
}

include('../admin/inc/config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexion: ' . $conn->connect_error]);
    exit;
}

$conn->set_charset('utf8mb4');

function comprimirImagen($origen, $destino, $calidad = 75) {
    $info = getimagesize($origen);
    if ($info === false) return false;

    switch ($info['mime']) {
        case 'image/jpeg':
            $imagen = imagecreatefromjpeg($origen);
            $ok = imagejpeg($imagen, $destino, $calidad);
            break;
        case 'image/png':
            $imagen = imagecreatefrompng($origen);
            $ok = imagepng($imagen, $destino, max(0, min(9, round((100 - $calidad) / 10))));
            break;
        case 'image/webp':
            $imagen = imagecreatefromwebp($origen);
            $ok = imagewebp($imagen, $destino, $calidad);
            break;
        default:
            return false;
    }

    if ($imagen) imagedestroy($imagen);
    return $ok;
}

try {
    $formularioId = isset($_POST['formulario_id']) ? intval($_POST['formulario_id']) : 0;
    if ($formularioId <= 0) {
        throw new Exception('Falta formulario_id valido.');
    }

    $formCheck = $conn->query("SELECT id FROM formulario WHERE id = '$formularioId' LIMIT 1");
    if (!$formCheck || $formCheck->num_rows === 0) {
        throw new Exception('Formulario no encontrado.');
    }

    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'clear') {
        $conn->query("DELETE FROM imagenes WHERE formulario_id = '$formularioId'");
        echo json_encode([
            'success' => true,
            'message' => 'Imagenes previas eliminadas.',
            'data' => ['formulario_id' => $formularioId]
        ]);
        $conn->close();
        exit;
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se recibio una imagen valida.');
    }

    $uploadDir = '/home/soluctem/sistema.soluctem.com.co/uploads/';
    $relativeDir = '/uploads/';

    if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
        throw new Exception('El directorio de uploads no esta disponible.');
    }

    $originalName = $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($extension, $allowed, true)) {
        throw new Exception('Tipo de imagen no permitido.');
    }

    $safeFilename = uniqid('img_', true) . '.' . $extension;
    $uploadFile = $uploadDir . $safeFilename;

    if (!comprimirImagen($tmpName, $uploadFile, 75)) {
        throw new Exception('Error al comprimir o guardar la imagen.');
    }

    $imagePath = $conn->real_escape_string($relativeDir . $safeFilename);
    $sqlImage = "INSERT INTO imagenes (formulario_id, imagen) VALUES ('$formularioId', '$imagePath')";
    if (!$conn->query($sqlImage)) {
        @unlink($uploadFile);
        throw new Exception('Error al guardar la imagen en la base de datos: ' . $conn->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Imagen enviada correctamente.',
        'data' => [
            'id' => $conn->insert_id,
            'formulario_id' => $formularioId,
            'imagen' => $relativeDir . $safeFilename
        ]
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>

