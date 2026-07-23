<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos de conexión a la base de datos
    include('../inc/config.php');

    // Conexión a la base de datos
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die('Error de conexión: ' . $conn->connect_error);
    }

    // Obtener y sanitizar datos del formulario
    $nombre_usuario = $conn->real_escape_string($_POST['nombre_usuario']);
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar_password'];
    $rol = $conn->real_escape_string($_POST['rol']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $apellido = $conn->real_escape_string($_POST['apellido']);
    $correo_electronico = $conn->real_escape_string($_POST['correo_electronico']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $activo = isset($_POST['activo']) ? intval($_POST['activo']) : 1; // Por defecto activo

    // Validar que las passwords coincidan
    if ($password !== $confirmar_password) {
        $_SESSION['error'] = 'Las contraseñas no coinciden.';
        header('Location: /admin/users/new.php');
        exit();
    }

    // Verificar que el nombre de usuario no exista
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = 'El nombre de usuario ya está en uso.';
        $stmt->close();
        $conn->close();
        header('Location: /admin/users/new.php');
        exit();
    }
    $stmt->close();

    // Verificar que el correo electrónico no exista
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo_electronico = ?");
    $stmt->bind_param("s", $correo_electronico);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = 'El correo electrónico ya está en uso.';
        $stmt->close();
        $conn->close();
        header('Location: /admin/users/new.php');
        exit();
    }
    $stmt->close();

    // Encriptar la password con bcrypt
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

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
            $_SESSION['error'] = 'Debe seleccionar al menos una zona.';
            $conn->close();
            header('Location: /admin/users/new.php');
            exit();
        }
    }

    // Convertir el array de zonas en una cadena separada por comas
    $zonas = implode(',', $zonas_array);

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
                    $_SESSION['error'] = 'Error al mover el archivo al directorio de destino.';
                    $conn->close();
                    header('Location: /admin/users/new.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'El tamaño del archivo excede el límite permitido de 2MB.';
                $conn->close();
                header('Location: /admin/users/new.php');
                exit();
            }
        } else {
            $_SESSION['error'] = 'Tipo de archivo no permitido. Solo se permiten archivos JPG, JPEG y PNG.';
            $conn->close();
            header('Location: /admin/users/new.php');
            exit();
        }
    }

    // Insertar usuario en la tabla 'usuarios'
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, password, rol, zonas, nombre, apellido, correo_electronico, telefono, activo, imagen_perfil) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssis", $nombre_usuario, $password_hash, $rol, $zonas, $nombre, $apellido, $correo_electronico, $telefono, $activo, $imagen_perfil);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Registro exitoso.';
        $stmt->close();
        $conn->close();
        header('Location: /admin/users/new.php');
        exit();
    } else {
        $_SESSION['error'] = 'Error al registrar el usuario: ' . $stmt->error;
        $stmt->close();
        $conn->close();
        header('Location: /admin/users/new.php');
        exit();
    }
}
?>



