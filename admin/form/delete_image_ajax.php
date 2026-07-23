<?php
// Conexión a la base de datos
 include('../inc/config.php');

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener el id de la imagen vía POST
$image_id = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;

if ($image_id > 0) {
    // Obtener la ruta de la imagen
    $sql_imagen = "SELECT imagen FROM imagenes WHERE id = $image_id";
    $resultado_imagen = $conexion->query($sql_imagen);

    if ($resultado_imagen->num_rows > 0) {
        $imagen = $resultado_imagen->fetch_assoc()['imagen'];

        // Convertir la ruta relativa a absoluta si es necesario
        $ruta_absoluta = '/home/soluctem/sistema.soluctem.com.co' . $imagen; // Asegúrate de que esta ruta es correcta

        // Eliminar la imagen del servidor
        if (file_exists($ruta_absoluta)) {
            if (unlink($ruta_absoluta)) {
                // Eliminar el registro de la base de datos
                $sql_delete = "DELETE FROM imagenes WHERE id = $image_id";
                if ($conexion->query($sql_delete)) {
                    echo 'success';
                } else {
                    echo 'error eliminando de la base de datos';
                }
            } else {
                echo 'error eliminando el archivo del servidor';
            }
        } else {
            echo 'archivo no existe';
        }
    } else {
        echo 'imagen no encontrada';
    }
} else {
    echo 'error en el ID de la imagen';
}

$conexion->close();
?>

