<?php
session_start();

// Almacenar un mensaje de desconexión antes de destruir la sesión en una cookie
setcookie("logout_success", "Has cerrado sesión correctamente.", time() + 10, "/");

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Redirigir al formulario de inicio de sesión
header("Location: /admin/login/");
exit();
?>

