<?php
session_start();

// Incluir la configuración de la base de datos
include('../inc/config.php');

// Verificar si el usuario está autenticado y tiene permisos de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
    $_SESSION['delete_error'] = 'No tiene permisos para realizar esta acción.';
    header('Location: index.php');
    exit();
}

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar el token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['delete_error'] = 'Token de seguridad inválido.';
        header('Location: index.php');
        exit();
    }

    // Verificar y sanitizar el ID del usuario
    if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        
        // Crear una conexión a la base de datos
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Verificar la conexión
        if ($conn->connect_error) {
            $_SESSION['delete_error'] = 'Error de conexión: ' . $conn->connect_error;
            header('Location: index.php');
            exit();
        }
        
        // Verificar si el usuario existe
        $stmt = $conn->prepare("SELECT imagen_perfil FROM usuarios WHERE id = ?");
        if (!$stmt) {
            $_SESSION['delete_error'] = 'Error en la preparación de la consulta.';
            $conn->close();
            header('Location: index.php');
            exit();
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($imagen_perfil);
        if (!$stmt->fetch()) {
            $_SESSION['delete_error'] = 'Usuario no encontrado.';
            $stmt->close();
            $conn->close();
            header('Location: index.php');
            exit();
        }
        $stmt->close();
        
        // Comenzar una transacción
        $conn->begin_transaction();
        
        try {
            // Eliminar el usuario de la base de datos
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Error en la preparación de la consulta de eliminación.');
            }
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            if ($stmt->affected_rows === 0) {
                throw new Exception('No se pudo eliminar el usuario.');
            }
            
            $stmt->close();
            
            // Eliminar la imagen de perfil del servidor si existe
            if (!empty($imagen_perfil)) {
                $imagen_path = '../uploads/perfiles/' . $imagen_perfil;
                if (file_exists($imagen_path)) {
                    if (!unlink($imagen_path)) {
                        throw new Exception('No se pudo eliminar la imagen de perfil.');
                    }
                }
            }
            
            // Confirmar la transacción
            $conn->commit();
            
            $_SESSION['delete_success'] = 'Usuario eliminado correctamente.';
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conn->rollback();
            $_SESSION['delete_error'] = 'Error al eliminar el usuario: ' . $e->getMessage();
        }
        
        // Cerrar la conexión
        $conn->close();
        
        // Redirigir al listado de usuarios
        header('Location: index.php');
        exit();
    } else {
        // Si no se ha enviado un ID válido
        $_SESSION['delete_error'] = 'ID de usuario inválido.';
        header('Location: index.php');
        exit();
    }
} else {
    // Si el método de solicitud no es POST
    $_SESSION['delete_error'] = 'Método de solicitud no permitido.';
    header('Location: index.php');
    exit();
}
?>

