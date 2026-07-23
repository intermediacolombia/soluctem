<?php include('../login/sesion.php');?>

<?php

// Datos de conexión a la base de datos
include('../inc/config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Obtener y sanitizar datos del formulario
$id_usuario = intval($_POST['id_usuario']);
$nombre = $conn->real_escape_string($_POST['nombre']);
$apellido = $conn->real_escape_string($_POST['apellido']);
$correo_electronico = $conn->real_escape_string($_POST['correo_electronico']);
$telefono = $conn->real_escape_string($_POST['telefono']);
$rol = $conn->real_escape_string($_POST['rol']);
$activo = isset($_POST['activo']) ? intval($_POST['activo']) : 1;

$password = $_POST['password'];
$confirmar_password = $_POST['confirmar_password'];

// Manejar las zonas seleccionadas
if ($rol === 'Administrador') {
    // Asignar todas las zonas
    $zonas_array = [
        'Amazonas', 'Antioquia', 'Arauca', 'Atlántico', 'Bolívar',
        'Boyacá', 'Caldas', 'Caquetá', 'Casanare', 'Cauca',
        'Cesar', 'Chocó', 'Córdoba', 'Cundinamarca', 'Guainía',
        'Guaviare', 'Huila', 'La Guajira', 'Magdalena', 'Meta',
        'Nariño', 'Norte de Santander', 'Putumayo', 'Quindío', 'Risaralda',
        'San Andrés y Providencia', 'Santander', 'Sucre', 'Tolima', 'Valle del Cauca',
        'Vaupés', 'Vichada'
    ];
} else {
    // Obtener las zonas seleccionadas
    if (isset($_POST['zonas']) && is_array($_POST['zonas']) && count($_POST['zonas']) > 0) {
        $zonas_array = $_POST['zonas'];
    } else {
        $_SESSION['update_error'] = 'Debe seleccionar al menos una zona.';
        $conn->close();
        header('Location: /admin/users/edit.php?id=' . $id_usuario);
        exit();
    }
}

// Convertir el array de zonas en una cadena separada por comas
$zonas = implode(',', $zonas_array);

// Validar que las contraseñas coincidan si se ingresaron
if (!empty($password) || !empty($confirmar_password)) {
    if ($password !== $confirmar_password) {
        $_SESSION['update_error'] = 'Las contraseñas no coinciden.';
        $conn->close();
        header('Location: /admin/users/edit.php?id=' . $id_usuario);
        exit();
    } else {
        // Encriptar la nueva contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
    }
}

// Verificar que el correo electrónico no exista en otro usuario
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo_electronico = ? AND id != ?");
$stmt->bind_param("si", $correo_electronico, $id_usuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['update_error'] = 'El correo electrónico ya está en uso por otro usuario.';
    $stmt->close();
    $conn->close();
    header('Location: /admin/users/edit.php?id=' . $id_usuario);
    exit();
}
$stmt->close();

// Manejar la carga de la imagen de perfil
$imagen_perfil = null;
if (isset($_FILES['imagen_perfil']) && $_FILES['imagen_perfil']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['imagen_perfil']['tmp_name'];
    $fileName = $_FILES['imagen_perfil']['name'];
    $fileSize = $_FILES['imagen_perfil']['size'];
    $fileType = $_FILES['imagen_perfil']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Extensiones permitidas
    $allowedfileExtensions = array('jpg', 'jpeg', 'png');

    if (in_array($fileExtension, $allowedfileExtensions)) {
        // Verificar el tamaño del archivo (máximo 2MB)
        if ($fileSize <= 2 * 1024 * 1024) {
            // Ruta donde se guardará la imagen
            $uploadFileDir = '../uploads/perfiles/';
            // Crear el directorio si no existe
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            // Generar un nombre de archivo único
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $imagen_perfil = $newFileName;
            } else {
                $_SESSION['update_error'] = 'Error al mover el archivo al directorio de destino.';
                $conn->close();
                header('Location: /admin/users/edit.php?id=' . $id_usuario);
                exit();
            }
        } else {
            $_SESSION['update_error'] = 'El tamaño del archivo excede el límite permitido de 2MB.';
            $conn->close();
            header('Location: /admin/users/edit.php?id=' . $id_usuario);
            exit();
        }
    } else {
        $_SESSION['update_error'] = 'Tipo de archivo no permitido. Solo se permiten archivos JPG, JPEG y PNG.';
        $conn->close();
        header('Location: /admin/users/edit.php?id=' . $id_usuario);
        exit();
    }
}

// Preparar la consulta de actualización
if (!empty($password)) {
    if ($imagen_perfil) {
        // Actualizar incluyendo la contraseña y la imagen de perfil
        $stmt = $conn->prepare("UPDATE usuarios SET password = ?, rol = ?, zonas = ?, nombre = ?, apellido = ?, correo_electronico = ?, telefono = ?, activo = ?, imagen_perfil = ? WHERE id = ?");
        $stmt->bind_param("sssssssisi", $password_hash, $rol, $zonas, $nombre, $apellido, $correo_electronico, $telefono, $activo, $imagen_perfil, $id_usuario);
    } else {
        // Actualizar incluyendo la contraseña sin cambiar la imagen de perfil
        $stmt = $conn->prepare("UPDATE usuarios SET password = ?, rol = ?, zonas = ?, nombre = ?, apellido = ?, correo_electronico = ?, telefono = ?, activo = ? WHERE id = ?");
        $stmt->bind_param("sssssssii", $password_hash, $rol, $zonas, $nombre, $apellido, $correo_electronico, $telefono, $activo, $id_usuario);
    }
} else {
    if ($imagen_perfil) {
        // Actualizar incluyendo la imagen de perfil sin cambiar la contraseña
        $stmt = $conn->prepare("UPDATE usuarios SET rol = ?, zonas = ?, nombre = ?, apellido = ?, correo_electronico = ?, telefono = ?, activo = ?, imagen_perfil = ? WHERE id = ?");
        $stmt->bind_param("ssssssisi", $rol, $zonas, $nombre, $apellido, $correo_electronico, $telefono, $activo, $imagen_perfil, $id_usuario);
    } else {
        // Actualizar sin cambiar la contraseña ni la imagen de perfil
        $stmt = $conn->prepare("UPDATE usuarios SET rol = ?, zonas = ?, nombre = ?, apellido = ?, correo_electronico = ?, telefono = ?, activo = ? WHERE id = ?");
        $stmt->bind_param("ssssssii", $rol, $zonas, $nombre, $apellido, $correo_electronico, $telefono, $activo, $id_usuario);
    }
}

if ($stmt->execute()) {
    $_SESSION['update_success'] = 'Datos actualizados correctamente.';
    $stmt->close();
    $conn->close();
    header('Location: /admin/users/edit.php?id=' . $id_usuario);
    exit();
} else {
    $_SESSION['update_error'] = 'Error al actualizar los datos: ' . $stmt->error;
    $stmt->close();
    $conn->close();
    header('Location: /admin/users/edit.php?id=' . $id_usuario);
    exit();
}
?>


