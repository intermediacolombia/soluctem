<?php
include('../login/sesion.php');

// Generar un token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Datos de conexión a la base de datos
include('../inc/config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Verificar si el usuario tiene rol de Administrador
if ($_SESSION['rol'] !== 'Administrador') {
    $no_permitido = true;
} else {
    // Obtener el ID del usuario a editar desde la URL
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id_usuario = intval($_GET['id']);
    } else {
        // Si no se proporciona un ID válido, redirigir al index
        header("Location: index.php");
        exit();
    }

    // Obtener los datos actuales del usuario, incluyendo la imagen de perfil
    $stmt = $conn->prepare("SELECT nombre_usuario, rol, zonas, nombre, apellido, correo_electronico, telefono, activo, imagen_perfil FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->bind_result($nombre_usuario, $rol, $zonas, $nombre, $apellido, $correo_electronico, $telefono, $activo, $imagen_perfil);
    $stmt->fetch();
    $stmt->close();

    // Convertir las zonas en un array
    $zonas_array = explode(',', $zonas);

    // Lista de departamentos
    $departamentos = [
        'Amazonas', 'Antioquia', 'Arauca', 'Atlántico', 'Bolívar',
        'Boyacá', 'Caldas', 'Caquetá', 'Casanare', 'Cauca',
        'Cesar', 'Chocó', 'Córdoba', 'Cundinamarca', 'Guainía',
        'Guaviare', 'Huila', 'La Guajira', 'Magdalena', 'Meta',
        'Nariño', 'Norte de Santander', 'Putumayo', 'Quindío', 'Risaralda',
        'San Andrés y Providencia', 'Santander', 'Sucre', 'Tolima', 'Valle del Cauca',
        'Vaupés', 'Vichada'
    ];

    // Ruta por defecto si no hay imagen de perfil
    $imagenRuta = '/admin/images/profile-placeholder.jpeg';
    if (!empty($imagen_perfil) && file_exists('../uploads/perfiles/' . $imagen_perfil)) {
        $imagenRuta = '/admin/uploads/perfiles/' . $imagen_perfil;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
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
		
		
		/*Select*/
		.custom-select-status {
    padding: 8px; /* Ajuste de padding para mantener todo dentro del campo */
    font-size: 1rem;
    border-radius: 5px; /* Redondeo suave para los bordes */
    border: 1px solid #ced4da; /* Color de borde más claro */
    width: 100%; /* Ajusta el select para ocupar todo el ancho del campo */
    color: #495057; /* Color de texto por defecto */
}

/* Colores específicos para cada opción */
.custom-select-status .option-activo {
    background-color: #d4f7d4; /* Verde claro */
    color: #155724; /* Texto más oscuro para mejor visibilidad */
}

.custom-select-status .option-inactivo {
    background-color: #f8d7da; /* Rojo claro */
    color: #721c24; /* Texto más oscuro para mejor visibilidad */
}

/* Colores dinámicos del select cuando se selecciona una opción */
.custom-select-status[data-selected="1"] {
    background-color: #d4f7d4; /* Verde claro */
    color: #155724; /* Texto más oscuro */
}

.custom-select-status[data-selected="0"] {
    background-color: #f8d7da; /* Rojo claro */
    color: #721c24; /* Texto más oscuro */
}

.custom-select-status:focus {
    border-color: #80bdff; /* Color de borde cuando el campo está enfocado */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.25); /* Sombra azul clara */
    outline: none;
}


    </style>
</head>
<body>
<?php include('../inc/menu.php'); ?>

<div class="container edit-container">
    <?php if (isset($no_permitido) && $no_permitido): ?>
        <!-- Mostrar mensaje de error si el usuario no es administrador -->
        <div class="alert alert-danger text-center">No tiene permisos para acceder a esta sección.</div>
    <?php else: ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="edit-card">
                <h2 class="text-center mb-4">Editar Usuario</h2>

                <!-- Mostrar mensajes de éxito o error -->
                <?php
                if (isset($_SESSION['update_success'])) {
                    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['update_success']) . '</div>';
                    unset($_SESSION['update_success']);
                }

                if (isset($_SESSION['update_error'])) {
                    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['update_error']) . '</div>';
                    unset($_SESSION['update_error']);
                }

                if (isset($_SESSION['delete_success'])) {
                    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['delete_success']) . '</div>';
                    unset($_SESSION['delete_success']);
                }

                if (isset($_SESSION['delete_error'])) {
                    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['delete_error']) . '</div>';
                    unset($_SESSION['delete_error']);
                }
                ?>

                <!-- Agregar enctype para permitir subida de archivos -->
                <form id="editarForm" method="POST" action="procesar_editar_usuario.php" enctype="multipart/form-data">
                    <!-- Vista previa de la imagen actual o seleccionada -->
                    <div class="text-center">
                        <img id="preview" src="<?php echo htmlspecialchars($imagenRuta); ?>" alt="Imagen de Perfil">
                    </div>

                    <!-- Campo para subir nueva imagen -->
                    <div class="form-group">
                        <label for="imagen_perfil">Cambiar Imagen de Perfil</label>
                        <input type="file" class="form-control-file" id="imagen_perfil" name="imagen_perfil" accept="image/*">
                        <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG. Tamaño máximo: 2MB.</small>
                    </div>

                    <!-- ID del Usuario (oculto) -->
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>">
                    <!-- Campo oculto para CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <!-- Nombre de Usuario (no editable) -->
                    <div class="form-group">
                        <label for="nombre_usuario">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($nombre_usuario); ?>" readonly>
                    </div>

                    <div class="form-row">
                        <!-- Contraseña -->
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

                    <div class="form-row">
                        <!-- Correo Electrónico -->
                        <div class="form-group col-md-6">
                            <label for="correo_electronico">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" value="<?php echo htmlspecialchars($correo_electronico); ?>" required>
                        </div>

                        <!-- Teléfono -->
                        <div class="form-group col-md-6">
                            <label for="telefono">Teléfono *</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Rol -->
                        <div class="form-group col-md-6">
                            <label for="rol">Rol *</label>
                            <select class="form-control" id="rol" name="rol" required>
                                <option value="Usuario" <?php echo ($rol === 'Usuario') ? 'selected' : ''; ?>>Usuario</option>
                                <option value="Administrador" <?php echo ($rol === 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                        </div>

                       <!-- Estado (Activo/Inactivo) -->
<div class="form-group col-md-6">
    <label for="activo">Estado *</label>
    <select class="form-control custom-select-status" id="activo" name="activo" required>
        <option value="1" class="option-activo" <?php echo ($activo == 1) ? 'selected' : ''; ?>>Activo</option>
        <option value="0" class="option-inactivo" <?php echo ($activo == 0) ? 'selected' : ''; ?>>Inactivo</option>
    </select>
</div>

                    </div>

                    <!-- Zonas -->
                    <div class="form-group">
                        <label>Zonas *</label>
                        <div id="zonasCheckboxes" class="border rounded p-2" style="max-height: 200px; overflow-y: scroll;">
                            <!-- Las zonas se generan con PHP -->
                            <div class="row">
                                <?php
                                $index = 0;
                                foreach ($departamentos as $departamento):
                                    ?>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input zona-checkbox" type="checkbox" name="zonas[]" value="<?php echo htmlspecialchars($departamento); ?>" <?php echo (in_array($departamento, $zonas_array)) ? 'checked' : ''; ?>>
                                            <label class="form-check-label"><?php echo htmlspecialchars($departamento); ?></label>
                                        </div>
                                    </div>
                                    <?php
                                    $index++;
                                    // Crear nueva fila cada tres elementos
                                    if ($index % 3 == 0 && $index != count($departamentos)) {
                                        echo '</div><div class="row">';
                                    }
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <button type="submit" class="btn btn-primary btn-block">Actualizar</button>
                </form>
                
                <!-- Botón de Eliminación -->
                <button type="button" class="btn btn-danger btn-block mt-3" data-toggle="modal" data-target="#deleteModal">
                    <i class="fa fa-trash"></i> Eliminar Usuario
                </button>

                <!-- Modal de Confirmación de Eliminación -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="delete_user.php" method="POST">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
							<center><img src="https://cdn-icons-gif.flaticon.com/11919/11919437.gif" width="150px"></center>
                          <!-- Campo oculto para pasar el ID del usuario -->
                          <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($id_usuario); ?>">
                          <!-- Campo oculto para CSRF Token -->
                          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                          <p>¿Está seguro de que desea eliminar al usuario <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong>? Esta acción no se puede deshacer.</p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                          <button type="submit" class="btn btn-danger">Eliminar</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>

            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Incluir Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- JavaScript personalizado -->
<script>
    $(document).ready(function() {
        <?php if (!isset($no_permitido) || !$no_permitido): ?>
        // Manejar la visualización de la contraseña
        $('.toggle-password').click(function() {
            const input = $(this).closest('.input-group').find('input');
            const icon = $(this).find('i');
            const type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            icon.toggleClass('fa-eye fa-eye-slash');
        });

        // Desactivar zonas si el rol es Administrador
        $('#rol').change(function() {
            if ($(this).val() === 'Administrador') {
                $('.zona-checkbox').prop('disabled', true);
                $('.zona-checkbox').prop('checked', true); // Seleccionar todas las zonas
            } else {
                $('.zona-checkbox').prop('disabled', false);
                // Opcional: Desmarcar todas las zonas
                // $('.zona-checkbox').prop('checked', false);
            }
        }).trigger('change'); // Activar el evento al cargar la página

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

            // Validar que al menos una zona esté seleccionada si el rol es Usuario
            if ($('#rol').val() === 'Usuario' && $('.zona-checkbox:checked').length === 0) {
                alert('Debe seleccionar al menos una zona.');
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
                $('#preview').attr('src', '<?php echo htmlspecialchars($imagenRuta); ?>');
            }
        });
        <?php endif; ?>
    });
</script>
	<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectStatus = document.getElementById("activo");

        function updateSelectColor() {
            if (selectStatus.value === "1") {
                selectStatus.style.backgroundColor = "#d4f7d4"; // Verde claro
                selectStatus.style.color = "#28a745"; // Texto verde
            } else {
                selectStatus.style.backgroundColor = "#f7d4d4"; // Rojo claro
                selectStatus.style.color = "#dc3545"; // Texto rojo
            }
        }

        // Inicializar color según el valor seleccionado
        updateSelectColor();

        // Cambiar color cuando se cambia la selección
        selectStatus.addEventListener("change", updateSelectColor);
    });
</script>


<?php include('../inc/menu-foot.php'); ?>
<?php include('../inc/footer.php'); ?>
</body>
</html>




