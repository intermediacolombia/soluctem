<?php
session_start();
include('../inc/config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

$conexion = new mysqli($servername, $username, $password, $dbname);
if ($conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

$formulario_id = isset($_POST['formulario_id']) ? intval($_POST['formulario_id']) : 0;
if ($formulario_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID de formulario inválido']);
    exit;
}

if (empty($_FILES['imagen']['name'])) {
    echo json_encode(['success' => false, 'error' => 'No se recibió ningún archivo']);
    exit;
}

$tmpName = $_FILES['imagen']['tmp_name'];
$name    = $_FILES['imagen']['name'];

$esImagenValida = getimagesize($tmpName);
if ($esImagenValida === false) {
    echo json_encode(['success' => false, 'error' => "Archivo no válido: " . htmlspecialchars($name)]);
    exit;
}

$uploadDir   = $ruta_base . '/uploads/';
$relativeDir = '/uploads/';

$timestamp     = round(microtime(true) * 1000);
$randomNumber  = mt_rand(1, 1000);
$extension     = strtolower(pathinfo($name, PATHINFO_EXTENSION));
$newFileName   = "image_" . $timestamp . "_" . $randomNumber . "." . $extension;
$uploadFile    = $uploadDir . $newFileName;

try {
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $img = imagecreatefromjpeg($tmpName);
            if (!$img) throw new Exception("No se pudo procesar la imagen JPG");
            imagejpeg($img, $uploadFile, 70);
            imagedestroy($img);
            break;

        case 'png':
            $img = imagecreatefrompng($tmpName);
            if (!$img) throw new Exception("No se pudo procesar la imagen PNG");
            imagepng($img, $uploadFile, 6);
            imagedestroy($img);
            break;

        case 'webp':
            $img = imagecreatefromwebp($tmpName);
            if (!$img) throw new Exception("No se pudo procesar la imagen WEBP");
            imagewebp($img, $uploadFile, 75);
            imagedestroy($img);
            break;

        default:
            if (!move_uploaded_file($tmpName, $uploadFile)) {
                throw new Exception("Error al subir el archivo");
            }
            break;
    }

    $imagePath = $conexion->real_escape_string($relativeDir . $newFileName);
    $sql = "INSERT INTO imagenes (formulario_id, imagen) VALUES ('$formulario_id', '$imagePath')";
    if (!$conexion->query($sql)) {
        throw new Exception('Error al guardar en base de datos: ' . $conexion->error);
    }

    echo json_encode(['success' => true, 'path' => $relativeDir . $newFileName]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conexion->close();
