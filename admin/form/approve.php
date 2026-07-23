<?php
// approve.php
session_start();
include('../inc/config.php');

$formulario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$redirect_params = array('id' => $formulario_id);
$allowed_redirect_params = array(
    'filterFechaInicio',
    'filterFechaFin',
    'filterTienda',
    'filterTecnico',
    'filterEstado',
    'search'
);

foreach ($allowed_redirect_params as $param) {
    if (isset($_GET[$param]) && $_GET[$param] !== '') {
        $redirect_params[$param] = $_GET[$param];
    }
}

$redirect_url = "/admin/form/?" . http_build_query($redirect_params);

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    $_SESSION['approve_error'] = "Conexion fallida: " . $conexion->connect_error;
    header("Location: $redirect_url");
    exit();
}

if ($formulario_id > 0) {
    $sql_update = "UPDATE formulario SET estado = 1 WHERE id = $formulario_id";
    if ($conexion->query($sql_update) === TRUE) {
        $_SESSION['approve_success'] = "Formulario aprobado correctamente.";
        header("Location: $redirect_url");
        exit();
    }

    $_SESSION['approve_error'] = "Error al actualizar el estado: " . $conexion->error;
    header("Location: $redirect_url");
    exit();
}

$_SESSION['approve_error'] = "ID de formulario no valido.";
header("Location: $redirect_url");
exit();
?>
