<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    // Si no ha iniciado sesión, redirigir al formulario de inicio de sesión
    header("Location: /admin/login/");
    exit();
}

// Obtener datos del usuario
$nombre_usuario = $_SESSION['nombre_usuario'];
$rol = $_SESSION['rol'];
$nombre_completo = $_SESSION['nombre'].' '.$_SESSION['apellido'];
?>