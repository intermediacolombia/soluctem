<?php
// authenticate.php

session_start();

// Verificar si se recibieron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Incluir los datos de conexión a la base de datos
    include('../inc/config.php');

    // Conexión a la base de datos
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Obtener y sanitizar los datos del formulario
    $nombre_usuario = $conn->real_escape_string($_POST['nombre_usuario']);
    $password = $_POST['password'];

    // Modificar la consulta SQL para incluir `nombre`, `apellido` y `zonas`
    $stmt = $conn->prepare("SELECT id, password, rol, activo, nombre, apellido, zonas, imagen_perfil FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si el usuario existe
    if ($stmt->num_rows > 0) {
        // Vincular las variables, incluyendo `nombre`, `apellido` y `zonas`
        $stmt->bind_result($id_usuario, $password_hash, $rol, $activo, $nombre, $apellido, $zonas, $imagen_perfil);
        $stmt->fetch();

        // Verificar la contraseña
        if (password_verify($password, $password_hash)) {
            // Verificar si el usuario está activo
            if ($activo == 1) {
                // Iniciar sesión y almacenar los datos en variables de sesión
                $_SESSION['id_usuario'] = $id_usuario;
                $_SESSION['nombre_usuario'] = $nombre_usuario;
                $_SESSION['rol'] = $rol;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['apellido'] = $apellido;
                $_SESSION['zonas'] = $zonas; // Almacenar las zonas del usuario
				$_SESSION['imagen_perfil'] = $imagen_perfil;

                // Redirigir al área protegida
                header("Location: /admin/");
                exit();
            } else {
                // Usuario inactivo
                $_SESSION['login_error'] = "Su cuenta está inactiva. Por favor, contacte al administrador.";
                header("Location: /admin/login/");
                exit();
            }
        } else {
            // Contraseña incorrecta
            $_SESSION['login_error'] = "Nombre de usuario o contraseña incorrectos.";
            header("Location: /admin/login/");
            exit();
        }
    } else {
        // Usuario no encontrado
        $_SESSION['login_error'] = "Nombre de usuario o contraseña incorrectos.";
        header("Location: /admin/login/");
        exit();
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
} else {
    // Si se intenta acceder directamente al script sin enviar el formulario
    header("Location: /admin/login/");
    exit();
}
?>


