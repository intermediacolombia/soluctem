<?php include('../login/sesion.php');?>
<?php
// Datos de conexión a la base de datos
include('../inc/config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Obtener el ID del usuario conectado
$id_usuario = $_SESSION['id_usuario'];

// Obtener los datos actuales del usuario, incluyendo la imagen de perfil
$stmt = $conn->prepare("SELECT nombre_usuario, nombre, apellido, correo_electronico, telefono, imagen_perfil FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($nombre_usuario, $nombre, $apellido, $correo_electronico, $telefono, $imagen_perfil);
$stmt->fetch();
$stmt->close();

// Ruta por defecto si no hay imagen de perfil
$imagenRuta = '/admin/images/profile-placeholder.jpeg';
if (!empty($imagen_perfil) && file_exists('../uploads/perfiles/' . $imagen_perfil)) {
    $imagenRuta = '/admin/uploads/perfiles/' . $imagen_perfil;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <?php include('../inc/header.php');?>
    <!-- Incluir Bootstrap CSS para estilos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Incluir FontAwesome para iconos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f7f7f7;
        }
        .profile-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .profile-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .profile-card .card-title {
            font-size: 24px;
            font-weight: bold;
        }
        .profile-card .list-group-item {
            border: none;
            padding-left: 0;
            padding-right: 0;
        }
        .profile-card .list-group-item strong {
            min-width: 150px;
            display: inline-block;
        }
        .edit-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php include('../inc/menu.php')?>
<div class="container profile-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Mostrar mensajes de éxito o error -->
            <?php
            if (isset($_SESSION['update_success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['update_success'] . '</div>';
                unset($_SESSION['update_success']);
            }

            if (isset($_SESSION['update_error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['update_error'] . '</div>';
                unset($_SESSION['update_error']);
            }
            ?>

            <div class="profile-card">
                <div class="text-center">
                    <!-- Mostrar la imagen de perfil -->
                    <img src="<?php echo $imagenRuta; ?>" alt="Imagen de Perfil" class="rounded-circle mb-3" style="
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 15px;
">
                </div>
                <h3 class="card-title text-center">Mi Perfil</h3>
                <ul class="list-group list-group-flush mt-4">
                    <li class="list-group-item">
                        <strong><i class="fa fa-user"></i> Nombre de Usuario:</strong> <?php echo htmlspecialchars($nombre_usuario); ?>
                    </li>
                    <li class="list-group-item">
                        <strong><i class="fa fa-id-card"></i> Nombre:</strong> <?php echo htmlspecialchars($nombre); ?>
                    </li>
                    <li class="list-group-item">
                        <strong><i class="fa fa-id-card-o"></i> Apellido:</strong> <?php echo htmlspecialchars($apellido); ?>
                    </li>
                    <li class="list-group-item">
                        <strong><i class="fa fa-envelope"></i> Correo Electrónico:</strong> <?php echo htmlspecialchars($correo_electronico); ?>
                    </li>
                    <li class="list-group-item">
                        <strong><i class="fa fa-phone"></i> Teléfono:</strong> <?php echo htmlspecialchars($telefono); ?>
                    </li>
                </ul>
                <!-- Botón para editar el perfil -->
                <div class="text-center edit-button">
                    <a href="edit_profile.php" class="btn btn-primary"><i class="fa fa-pencil"></i> Editar Perfil</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../inc/menu-foot.php');?>
<?php include('../inc/footer.php');?>

<!-- Incluir jQuery--->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->

<!-- Incluir Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

