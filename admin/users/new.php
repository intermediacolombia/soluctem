<?php include('../login/sesion.php');?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
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
        .register-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .register-card {
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
            display: none; /* Ocultar inicialmente */
        }
		
		/*Activo inactivo*/
		.estado-activo {
    background-color: #d4f7d4; /* Verde claro */
    color: #155724; /* Verde oscuro para el texto */
}

.estado-inactivo {
    background-color: #f8d7da; /* Rojo claro */
    color: #721c24; /* Rojo oscuro para el texto */
}

    </style>
</head>
<body>
<?php include('../inc/menu.php'); ?>

<div class="container register-container">
    <?php
    // Verificar si el usuario tiene rol de Administrador
    if ($_SESSION['rol'] !== 'Administrador') {
        // Mostrar mensaje de error
        echo '<div class="alert alert-danger text-center">No tiene permisos para acceder a esta sección.</div>';
    } else {
        // Mostrar el formulario de registro
    ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="register-card">
                <h2 class="text-center mb-4">Registro de Usuario</h2>

                <!-- Mostrar mensajes de éxito o error -->
                <?php
                if (isset($_SESSION['success'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                    unset($_SESSION['success']);
                }

                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                ?>

                <!-- Agregar enctype para permitir subida de archivos -->
                <form id="registroForm" method="POST" action="procesar_registro.php" enctype="multipart/form-data">
                    <!-- Vista previa de la imagen seleccionada -->
                    <div class="text-center">
                        <img id="preview" src="#" alt="Vista previa de la imagen">
                    </div>

                    <div class="form-row">
                        <!-- Nombre de Usuario -->
                        <div class="form-group col-md-6">
                            <label for="nombre_usuario">Nombre de Usuario *</label>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
                        </div>

                        <!-- Correo Electrónico -->
                        <div class="form-group col-md-6">
                            <label for="correo_electronico">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Contraseña -->
                        <div class="form-group col-md-6">
                            <label for="password">Contraseña *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="form-group col-md-6">
                            <label for="confirmar_password">Confirmar Contraseña *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
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
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <!-- Apellido -->
                        <div class="form-group col-md-6">
                            <label for="apellido">Apellido *</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Teléfono -->
                        <div class="form-group col-md-6">
                            <label for="telefono">Teléfono *</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>

                        <!-- Rol -->
                        <div class="form-group col-md-6">
                            <label for="rol">Rol *</label>
                            <select class="form-control" id="rol" name="rol" required>
                                <option value="Usuario">Usuario</option>
                                <option value="Administrador">Administrador</option>
                            </select>
                        </div>
                    </div>

                    <!-- Nueva sección para la imagen de perfil -->
                    <div class="form-group">
                        <label for="imagen_perfil">Imagen de Perfil (Opcional)</label>
                        <input type="file" class="form-control-file" id="imagen_perfil" name="imagen_perfil" accept="image/*">
                        <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG. Tamaño máximo: 2MB.</small>
                    </div>

                    <!-- Zonas -->
                    <div class="form-group">
                        <label>Zonas *</label>
                        <div id="zonasCheckboxes" class="border rounded p-2" style="max-height: 200px; overflow-y: scroll;">
                            <!-- Las zonas se generarán con JavaScript -->
                        </div>
                    </div>

                    <div class="form-group">
    <label for="activo">Estado *</label>
    <select class="form-control estado-select" id="activo" name="activo" required>
        <option value="1">Activo</option>
        <option value="0">Inactivo</option>
    </select>
</div>


                    <!-- Botón de Envío -->
                    <button type="submit" class="btn btn-primary btn-block">Registrar</button>
                </form>
            </div>
        </div>
    </div>
    <?php
    } // Fin del else
    ?>
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

        // Desactivar zonas si el rol es Administrador
        $('#rol').change(function() {
            if ($(this).val() === 'Administrador') {
                $('.zona-checkbox').prop('disabled', true);
                $('.zona-checkbox').prop('checked', true); // Seleccionar todas las zonas
            } else {
                $('.zona-checkbox').prop('disabled', false);
                $('.zona-checkbox').prop('checked', false);
            }
        }).trigger('change'); // Activar el evento al cargar la página

        // Validación en tiempo real de las contraseñas
        $('#password, #confirmar_password').on('keyup', function() {
            const password = $('#password').val();
            const confirmarpassword = $('#confirmar_password').val();

            if (password === '' || confirmarpassword === '') {
                $('#passwordHelp').text('');
            } else if (password === confirmarpassword) {
                $('#passwordHelp').text('Las contraseñas coinciden.').removeClass('password-mismatch').addClass('password-match');
            } else {
                $('#passwordHelp').text('Las contraseñas no coinciden.').removeClass('password-match').addClass('password-mismatch');
            }
        });

        // Validar que las contraseñas coincidan antes de enviar el formulario
        $('#registroForm').submit(function(event) {
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

        // Generar los checkboxes de las zonas solo si el usuario es administrador
        <?php if ($_SESSION['rol'] === 'Administrador'): ?>
        const departamentos = [
            'Amazonas', 'Antioquia', 'Arauca', 'Atlántico', 'Bolívar',
            'Boyacá', 'Caldas', 'Caquetá', 'Casanare', 'Cauca',
            'Cesar', 'Chocó', 'Córdoba', 'Cundinamarca', 'Guainía',
            'Guaviare', 'Huila', 'La Guajira', 'Magdalena', 'Meta',
            'Nariño', 'Norte de Santander', 'Putumayo', 'Quindío', 'Risaralda',
            'San Andrés y Providencia', 'Santander', 'Sucre', 'Tolima', 'Valle del Cauca',
            'Vaupés', 'Vichada'
        ];

        // Organizar los departamentos en columnas
        let zonasHtml = '<div class="row">';
        departamentos.forEach(function(departamento, index) {
            zonasHtml += `
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input zona-checkbox" type="checkbox" name="zonas[]" value="${departamento}">
                        <label class="form-check-label">${departamento}</label>
                    </div>
                </div>
            `;
            // Crear nueva fila cada tres elementos
            if ((index + 1) % 3 === 0 && index !== departamentos.length - 1) {
                zonasHtml += '</div><div class="row">';
            }
        });
        zonasHtml += '</div>';
        $('#zonasCheckboxes').html(zonasHtml);
        <?php endif; ?>

        // Mostrar vista previa de la imagen seleccionada
        $('#imagen_perfil').change(function() {
            const input = this;
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result);
                    $('#preview').show();
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                // Si no hay archivo seleccionado, ocultar la vista previa
                $('#preview').attr('src', '#');
                $('#preview').hide();
            }
        });
    });
</script>
	
	<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectElement = document.getElementById('activo');

    // Función para aplicar estilo según la selección
    function applySelectStyle() {
        if (selectElement.value === "1") {
            selectElement.classList.remove('estado-inactivo');
            selectElement.classList.add('estado-activo');
        } else {
            selectElement.classList.remove('estado-activo');
            selectElement.classList.add('estado-inactivo');
        }
    }

    // Aplicar el estilo inicial
    applySelectStyle();

    // Actualizar el estilo al cambiar la selección
    selectElement.addEventListener('change', applySelectStyle);
});
</script>

<?php include('../inc/menu-foot.php');?>
<?php include('../inc/footer.php');?>
</body>
</html>





