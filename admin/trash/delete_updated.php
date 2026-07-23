<?php
session_start();
include('../inc/config.php');

// Verificar autenticación
if (!isset($_SESSION['email'])) {
    $_SESSION['delete_error'] = 'No tiene permisos para realizar esta acción.';
    header('Location: ../index.php');
    exit();
}

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    $_SESSION['delete_error'] = 'Error de conexión a la base de datos.';
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $formulario_id = intval($_POST['id']);
    
    // Verificar que el formulario existe y no está ya borrado
    $sql_check = "SELECT id, nombreSolicitante, numeroTicket FROM formulario WHERE id = ? AND borrado = 0";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $formulario_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows === 0) {
        $_SESSION['delete_error'] = 'El formulario no existe o ya fue eliminado.';
        header('Location: ../form-list/');
        exit();
    }
    
    $formulario = $result_check->fetch_assoc();
    
    // Marcar como borrado (soft delete) y guardar información de quién y cuándo
    $fecha_eliminacion = date('Y-m-d H:i:s');
    $usuario_eliminacion = $_SESSION['nombre'];
    
    $sql_delete = "UPDATE formulario 
                   SET borrado = 1, 
                       fecha_eliminacion = ?, 
                       usuario_eliminacion = ? 
                   WHERE id = ? AND borrado = 0";
    
    $stmt_delete = $conexion->prepare($sql_delete);
    $stmt_delete->bind_param("ssi", $fecha_eliminacion, $usuario_eliminacion, $formulario_id);
    
    if ($stmt_delete->execute() && $stmt_delete->affected_rows > 0) {
        // Registrar en log
        $log_message = "Formulario ID $formulario_id movido a papelera por " . $_SESSION['nombre'] . " (" . $_SESSION['email'] . ") el $fecha_eliminacion";
        error_log($log_message, 3, "../logs/deletions.log");
        
        $_SESSION['delete_success'] = "El formulario #$formulario_id (Ticket: " . htmlspecialchars($formulario['numeroTicket']) . ") ha sido movido a la papelera. Los administradores pueden recuperarlo durante los próximos 3 meses.";
        header('Location: ../form-list/');
        exit();
    } else {
        $_SESSION['delete_error'] = 'No se pudo eliminar el formulario. Intente nuevamente.';
        header('Location: ../form/?id=' . $formulario_id);
        exit();
    }
    
} else {
    $_SESSION['delete_error'] = 'Solicitud inválida.';
    header('Location: ../form-list/');
    exit();
}

$conexion->close();
?>
