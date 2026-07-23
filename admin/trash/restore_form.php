<?php
session_start();
include('../inc/config.php');

// Verificar que sea administrador
if ($_SESSION['rol'] !== 'Administrador') {
    $_SESSION['restore_error'] = 'No tiene permisos para realizar esta acción.';
    header('Location: index.php');
    exit();
}

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    $_SESSION['restore_error'] = 'Error de conexión a la base de datos.';
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $formulario_id = intval($_POST['id']);
    
    // Verificar que el formulario esté en papelera (borrado = 1)
    $sql_check = "SELECT id, nombreSolicitante, nombreTienda, numeroTicket FROM formulario WHERE id = ? AND borrado = 1";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $formulario_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows === 0) {
        $_SESSION['restore_error'] = 'El formulario no existe en la papelera o ya fue recuperado.';
        header('Location: index.php');
        exit();
    }
    
    $formulario = $result_check->fetch_assoc();
    
    // Recuperar el formulario (cambiar borrado = 0 y limpiar datos de eliminación)
    $sql_restore = "UPDATE formulario 
                    SET borrado = 0, 
                        fecha_eliminacion = NULL, 
                        usuario_eliminacion = NULL 
                    WHERE id = ? AND borrado = 1";
    
    $stmt_restore = $conexion->prepare($sql_restore);
    $stmt_restore->bind_param("i", $formulario_id);
    
    if ($stmt_restore->execute() && $stmt_restore->affected_rows > 0) {
        // Registrar la acción en log
        $log_message = "Formulario ID $formulario_id recuperado de papelera por " . $_SESSION['nombre'] . " (" . $_SESSION['email'] . ") el " . date('Y-m-d H:i:s');
        error_log($log_message, 3, "../logs/restores.log");
        
        $_SESSION['restore_success'] = "El formulario #$formulario_id (Ticket: " . htmlspecialchars($formulario['numeroTicket']) . ") ha sido recuperado exitosamente.";
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['restore_error'] = 'No se pudo recuperar el formulario. Intente nuevamente.';
        header('Location: view_deleted.php?id=' . $formulario_id);
        exit();
    }
    
} else {
    $_SESSION['restore_error'] = 'Solicitud inválida.';
    header('Location: index.php');
    exit();
}

$conexion->close();
?>
