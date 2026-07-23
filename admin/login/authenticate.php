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

    // Configurar la zona horaria de Colombia
    date_default_timezone_set('America/Bogota');
    $fecha_actual = date('Y-m-d H:i:s');

    // Obtener la dirección IP del usuario
    $ip_usuario = $_SERVER['REMOTE_ADDR'];

    // Obtener y sanitizar los datos del formulario
    $nombre_usuario = $conn->real_escape_string($_POST['nombre_usuario']);
    $password = $_POST['password'];

    // Modificar la consulta SQL para incluir `nombre`, `apellido`, `zonas`, `intentos_fallidos`, `ultimo_intento`, `ultimo_login`, y `ip_ultimo_login`
    $stmt = $conn->prepare("SELECT id, password, rol, activo, nombre, apellido, zonas, imagen_perfil, intentos_fallidos, ultimo_intento FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si el usuario existe
    if ($stmt->num_rows > 0) {
        // Vincular las variables, incluyendo `nombre`, `apellido`, `zonas`, `intentos_fallidos`, `ultimo_intento`
        $stmt->bind_result($id_usuario, $password_hash, $rol, $activo, $nombre, $apellido, $zonas, $imagen_perfil, $intentos_fallidos, $ultimo_intento);
        $stmt->fetch();

        // Verificar si el usuario ha alcanzado 5 intentos fallidos
        if ($intentos_fallidos >= 4) {
            // Bloquear la cuenta al 5to intento fallido
            $stmt = $conn->prepare("UPDATE usuarios SET activo = 0, intentos_fallidos = intentos_fallidos + 1, ultimo_intento = NOW() WHERE id = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();

            // Mostrar mensaje de bloqueo
            $_SESSION['login_error'] = "Su usuario ha sido bloqueado después de 5 intentos fallidos. Por favor, contacte al administrador.";
            header("Location: /admin/login/");
            exit();
        }

        // Verificar la contraseña
        if (password_verify($password, $password_hash)) {
            // Verificar si el usuario está activo
            if ($activo == 1) {
                // Reiniciar intentos fallidos, actualizar la fecha del último intento, la fecha del último login y la IP
                $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = 0, ultimo_intento = NOW(), ultimo_login = ?, ip_ultimo_login = ? WHERE id = ?");
                $stmt->bind_param("ssi", $fecha_actual, $ip_usuario, $id_usuario);
                $stmt->execute();

                // Iniciar sesión y almacenar los datos en variables de sesión
                $_SESSION['id_usuario'] = $id_usuario;
                $_SESSION['nombre_usuario'] = $nombre_usuario;
                $_SESSION['rol'] = $rol;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['apellido'] = $apellido;
                $_SESSION['zonas'] = $zonas;
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
            // Contraseña incorrecta, incrementar los intentos fallidos y actualizar la fecha del último intento
            $stmt = $conn->prepare("UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1, ultimo_intento = NOW() WHERE id = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();

            // Mostrar el número de intentos restantes
            $intentos_restantes = 5 - ($intentos_fallidos + 1);
            if ($intentos_restantes > 0) {
                $_SESSION['login_error'] = "Nombre de usuario o contraseña incorrectos. Tienes $intentos_restantes intento(s) más.";
            } else {
                $_SESSION['login_error'] = "Tu cuenta ha sido bloqueada después de 5 intentos fallidos. Contacta al administrador.";
            }

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





