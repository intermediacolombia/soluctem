<?php
session_start();

// Verificar si el usuario tiene el rol de 'Administrador'
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador' || $_SESSION['rol'] !== 'Usuario') {
    $_SESSION['delete_error'] = "No tienes permisos para realizar esta acción.";
    header("Location: /admin/form-list");
    exit();
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['delete_error'] = "Solicitud no válida.";
    header("Location: /admin/form-list");
    exit();
}

// Definir el token fijo
define("FIXED_CSRF_TOKEN", "FoKjYUV5DwBSQ8eOoozVkxKjMpIcZtCTgDwxhVeMAPj5k1dI35cZ0R9XP8vp6ChwPxsWWlLIkeSXuvyorYhMBJaPPh3vku2lsmTidLcZgELxEQj6ce784JVlI4we3yasGEE2qYvmfLiJPXb3lgGD8xktIerJTteFR8189o2RpcvrxcujzcqkPkAS0fDOrUF2ofbXOWUPfPWq5jReQ5lchyVkHuJC6yI9eixckQ0rZSXVwYiqVG2aWLP3SYzrB5bMqOb9ZgB0awoKzkw8bLs0eqgtB2uSccUbhI8qrtL4oR9zUM1HEtxSkxZOkWVUFPx4Bzzl7uicqpvxKrws9iEmywaeESifPLWRXtTTqxTQg4RJh7DGKFBfOU2Eho7IsdT5Hfh8zogIGHB8IKgRcu7HAx1mOIF5GRcgUwv7XML5IV0cqYIc6emv8Ubuy1oZEdUeH7EpeC7SCcZBO2O4oad6MxRTYALD3kSVrdp0tA61cLQJV3xfkRiD4vFflmfadqpR");

// Verificar el token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== FIXED_CSRF_TOKEN) {
    $_SESSION['delete_error'] = "Token CSRF no válido.";
    header("Location: /admin/form-list");
    exit();
}

// Conectar a la base de datos
include('../inc/config.php');
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener el id del formulario desde el POST
$formulario_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($formulario_id > 0) {
    // Consulta SQL para verificar si el formulario existe y no está aprobado
    $sql_verificar = "SELECT estado FROM formulario WHERE id = $formulario_id";
    $resultado_verificar = $conexion->query($sql_verificar);

    if ($resultado_verificar->num_rows > 0) {
        $row = $resultado_verificar->fetch_assoc();

        if ($row['estado'] == 0) { // Solo eliminar si el estado es 0 (no aprobado)
            // Eliminar el formulario y las imágenes asociadas
            $sql_delete_formulario = "DELETE FROM formulario WHERE id = $formulario_id";
            $sql_delete_imagenes = "DELETE FROM imagenes WHERE formulario_id = $formulario_id";

            if ($conexion->query($sql_delete_formulario) === TRUE && $conexion->query($sql_delete_imagenes) === TRUE) {
                $_SESSION['delete_success'] = "Formulario eliminado exitosamente.";
            } else {
                $_SESSION['delete_error'] = "Error al eliminar el formulario: " . $conexion->error;
            }
        } else {
            $_SESSION['delete_error'] = "No se puede eliminar el formulario porque ya está aprobado.";
        }
    } else {
        $_SESSION['delete_error'] = "Formulario no encontrado.";
    }
} else {
    $_SESSION['delete_error'] = "ID de formulario no válido.";
}

// Cerrar la conexión
$conexion->close();

// Redirigir al usuario a la lista de formularios
header("Location: /admin/form-list");
exit();
?>




