<?php include('../inc/config.php');?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
    <!-- Incluir Bootstrap CSS para estilos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Incluir FontAwesome para iconos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f7f7f7;
        }
        .login-container {
            margin-top: 100px;
        }
        .login-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .login-logo {
            width: 150px;
            margin: 0 auto 30px;
            display: block;
        }
        .toggle-password {
            cursor: pointer;
        }
        .toggle-password .fa {
            font-size: 18px;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="login-card">
                <!-- Logo -->
                <img src="<?php echo $logo;?>" alt="Logo" class="login-logo">
<?php
if (isset($_COOKIE['logout_success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_COOKIE['logout_success']) . '
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>';
    // Eliminar la cookie después de mostrar el mensaje
    setcookie("logout_success", "", time() - 3600, "/");
}
?>


                <h3 class="text-center">Inicio de Sesión</h3>

                <!-- Mostrar mensajes de error -->
                <?php
                session_start();
                if (isset($_SESSION['login_error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['login_error'] . '</div>';
                    unset($_SESSION['login_error']);
                }
                ?>

                <form id="loginForm" method="POST" action="authenticate.php">
                    <!-- Nombre de Usuario -->
                    <div class="form-group">
                        <label for="nombre_usuario">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Incluir Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- JavaScript para mostrar/ocultar contraseña -->
<script>
    $(document).ready(function() {
        // Manejar la visualización de la contraseña
        $('.toggle-password').click(function() {
            const input = $('#password');
            const icon = $(this).find('i');
            const type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            icon.toggleClass('fa-eye fa-eye-slash');
        });
    });
</script>

</body>
</html>

