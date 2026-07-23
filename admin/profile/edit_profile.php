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
    <title>Editar Perfil</title>
    <?php include('../inc/header.php');?>
    <!-- Incluir Bootstrap CSS para estilos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Incluir FontAwesome para iconos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f7f7f7;
        }
        .edit-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .edit-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .password-match {
            color: green;
        }
        .password-mismatch {
            color: red;
        }
        .toggle-password {
            cursor: pointer;
        }
        .toggle-password .fa {
            font-size: 18px;
            color: #6c757d;
        }
        /* Estilos para la imagen de vista previa */
        #preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<?php include('../inc/menu.php')?>
<div class="container edit-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="edit-card">
                <h2 class="text-center mb-4">Editar Perfil</h2>

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

                <!-- Agregar enctype para permitir subida de archivos -->
                <form id="editarForm" method="POST" action="procesar_editar_perfil.php" enctype="multipart/form-data">
                    <!-- Vista previa de la imagen actual o seleccionada -->
                    <div class="text-center">
                        <img id="preview" src="<?php echo $imagenRuta; ?>" alt="Imagen de Perfil">
                    </div>

                    <!-- Campo para subir nueva imagen -->
                    <div class="form-group">
                        <label for="imagen_perfil">Cambiar Imagen de Perfil</label>
                        <input type="file" class="form-control-file" id="imagen_perfil" name="imagen_perfil" accept="image/*">
                        <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG. Tamaño máximo: 2MB.</small>
                    </div>

                    <!-- Nombre de Usuario (no editable) -->
                    <div class="form-group">
                        <label for="nombre_usuario">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($nombre_usuario); ?>" readonly>
                    </div>

                    <div class="form-row">
                        <!-- Nueva Contraseña -->
                        <div class="form-group col-md-6">
                            <label for="password">Nueva Contraseña (dejar en blanco si no desea cambiarla)</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="form-group col-md-6">
                            <label for="confirmar_password">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmar_password" name="confirmar_password">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <small id="passwordHelp" class="form-text"></small>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Nombre -->
                        <div class="form-group col-md-6">
                            <label for="nombre">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                        </div>

                        <!-- Apellido -->
                        <div class="form-group col-md-6">
                            <label for="apellido">Apellido *</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
                        </div>
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="form-group">
                        <label for="correo_electronico">Correo Electrónico *</label>
                        <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" value="<?php echo htmlspecialchars($correo_electronico); ?>" required>
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group">
                        <label for="telefono">Teléfono *</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
                    </div>

                    <!-- Botón de Envío -->
                    <button type="submit" class="btn btn-primary btn-block">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Incluir Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- JavaScript personalizado -->
<script>
    $(document).ready(function() {
        // Manejar la visualización de la contraseña
        $('.toggle-password').click(function() {
            const input = $(this).closest('.input-group').find('input');
            const icon = $(this).find('i');
            const type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            icon.toggleClass('fa-eye fa-eye-slash');
        });

        // Validación en tiempo real de las contraseñas
        $('#password, #confirmar_password').on('keyup', function() {
            const password = $('#password').val();
            const confirmarpassword = $('#confirmar_password').val();

            if (password === '' && confirmarpassword === '') {
                $('#passwordHelp').text('');
            } else if (password === confirmarpassword) {
                $('#passwordHelp').text('Las contraseñas coinciden.').removeClass('password-mismatch').addClass('password-match');
            } else {
                $('#passwordHelp').text('Las contraseñas no coinciden.').removeClass('password-match').addClass('password-mismatch');
            }
        });

        // Validar que las contraseñas coincidan antes de enviar el formulario
        $('#editarForm').submit(function(event) {
            const password = $('#password').val();
            const confirmarpassword = $('#confirmar_password').val();

            if (password !== confirmarpassword) {
                alert('Las contraseñas no coinciden.');
                event.preventDefault();
            }
        });

        // Mostrar vista previa de la imagen seleccionada
        $('#imagen_perfil').change(function() {
            const input = this;
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                // Si no hay archivo seleccionado, mostrar la imagen actual
                $('#preview').attr('src', '<?php echo $imagenRuta; ?>');
            }
        });
    });
</script>
<?php include('../inc/menu-foot.php');?>
<?php include('../inc/footer.php');?>
</body>
</html>

