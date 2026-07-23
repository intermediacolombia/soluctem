<?php
session_start();
include('../inc/config.php');

// Verificar que sea administrador
if ($_SESSION['rol'] !== 'Administrador') {
    $_SESSION['permanent_delete_error'] = 'No tiene permisos para realizar esta acción.';
    header('Location: index.php');
    exit();
}

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    $_SESSION['permanent_delete_error'] = 'Error de conexión a la base de datos.';
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $formulario_id = intval($_POST['id']);
    
    // Verificar que el formulario esté en papelera (borrado = 1)
    $sql_check = "SELECT id FROM formulario WHERE id = ? AND borrado = 1";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $formulario_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows === 0) {
        $_SESSION['permanent_delete_error'] = 'El formulario no existe en la papelera o ya fue eliminado.';
        header('Location: index.php');
        exit();
    }
    
    // Iniciar transacción
    $conexion->begin_transaction();
    
    try {
        // 1. Eliminar imágenes asociadas
        $sql_delete_images = "DELETE FROM imagenes WHERE formulario_id = ?";
        $stmt_images = $conexion->prepare($sql_delete_images);
        $stmt_images->bind_param("i", $formulario_id);
        $stmt_images->execute();
        
        // 2. Eliminar el formulario DEFINITIVAMENTE de la base de datos
        $sql_delete_form = "DELETE FROM formulario WHERE id = ? AND borrado = 1";
        $stmt_form = $conexion->prepare($sql_delete_form);
        $stmt_form->bind_param("i", $formulario_id);
        $stmt_form->execute();
        
        if ($stmt_form->affected_rows > 0) {
            // Commit de la transacción
            $conexion->commit();
            
            // Registrar la acción en log (opcional)
            $log_message = "Formulario ID $formulario_id eliminado PERMANENTEMENTE por " . $_SESSION['nombre'] . " (" . $_SESSION['email'] . ") el " . date('Y-m-d H:i:s');
            error_log($log_message, 3, "../logs/permanent_deletions.log");
            
            $_SESSION['delete_success'] = "El formulario #$formulario_id ha sido eliminado PERMANENTEMENTE de la base de datos.";
            header('Location: index.php');
            exit();
        } else {
            throw new Exception('No se pudo eliminar el formulario.');
        }
        
    } catch (Exception $e) {
        // Rollback en caso de error
        $conexion->rollback();
        $_SESSION['permanent_delete_error'] = 'Error al eliminar el formulario: ' . $e->getMessage();
        header('Location: view_deleted.php?id=' . $formulario_id);
        exit();
    }
    
} else {
    $_SESSION['permanent_delete_error'] = 'Solicitud inválida.';
    header('Location: index.php');
    exit();
}

$conexion->close();
?>
