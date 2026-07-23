<?php
include('../login/sesion.php');

// Datos de conexión a la base de datos
include('../inc/config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Obtener todos los usuarios (solo si el usuario es administrador)
if ($_SESSION['rol'] === 'Administrador') {
    $sql = "SELECT id, nombre_usuario, nombre, apellido, correo_electronico, rol, activo, ultimo_login, ip_ultimo_login FROM usuarios";

    $resultado_usuarios = $conn->query($sql);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Usuarios</title>
    <?php include('../inc/header.php'); ?>
    <!-- Incluir Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluir DataTables CSS para Bootstrap -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Incluir Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Incluir DataTables JS para Bootstrap -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <style>
		 table.dataTable {
        width: 100%; /* Mantener el ancho al 100% */
    }
		
		 table.dataTable td,
    table.dataTable th {
        padding: 4px 8px; /* Reducir el padding a la mitad */
        font-size: 14px; /* Reducir el tamaño de la fuente */
    }
        /* Estilos para hacer que las filas sean clicables */
        table tbody tr {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        /* Estilo para cambiar el color de fondo al pasar el cursor */
        table tbody tr:hover {
            background-color: #d0d0d0; /* Un color más oscuro para el hover */
        }
		
		/*Activo inactivo*/
		.estado-activo {
    background-color: #d4f7d4; /* Verde claro */
    color: #155724; /* Verde oscuro para el texto */
    padding: 8px; /* Añadir algo de padding para que el contenido se vea mejor */
    border-radius: 5px; /* Bordes suaves */
    font-weight: bold; /* Texto en negrita para darle énfasis */
    text-align: center; /* Centrar el texto en la celda */
}

.estado-inactivo {
    background-color: #f8d7da; /* Rojo claro */
    color: #721c24; /* Rojo oscuro para el texto */
    padding: 8px; /* Añadir algo de padding para que el contenido se vea mejor */
    border-radius: 5px; /* Bordes suaves */
    font-weight: bold; /* Texto en negrita para darle énfasis */
    text-align: center; /* Centrar el texto en la celda */
}
		#date-last{
			font-size: 9px;
		}

    </style>
</head>
<body>
<?php include('../inc/menu.php'); ?>
<div class="container mt-5">
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
    <h1 class="text-center mb-4">Administrar Usuarios</h1>

    <?php
    // Verificar si el usuario tiene rol de Administrador
    if ($_SESSION['rol'] !== 'Administrador') {
        // Mostrar mensaje de error
        echo '<div class="alert alert-danger text-center">No tiene permisos para acceder a esta sección.</div>';
    } else {
        // Mostrar el contenido restringido (tabla de usuarios)
    ?>
        <div class="table-responsive">
            <table id="usuarios" class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo Electrónico</th>
                        <th>Rol</th>
						<th>Último Login</th>
						<th>IP</th>

                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado_usuarios->num_rows > 0): ?>
                        <?php while ($row = $resultado_usuarios->fetch_assoc()): ?>
                            <tr data-href="edit.php?id=<?php echo $row['id']; ?>">
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nombre_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($row['correo_electronico']); ?></td>
                                <td><?php echo htmlspecialchars($row['rol']); ?></td>
								<td id="date-last"><?php echo htmlspecialchars($row['ultimo_login']); ?></td>
								<td><?php echo htmlspecialchars($row['ip_ultimo_login']); ?></td>

                                <td class="<?php echo ($row['activo'] == 1) ? 'estado-activo' : 'estado-inactivo'; ?>">
    <?php echo ($row['activo'] == 1) ? 'Activo' : 'Inactivo'; ?>
</td>

                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No se encontraron usuarios.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php
    } // Fin del else
    ?>
</div>

<script>
    // Inicializar DataTables solo si el usuario es administrador
    <?php if ($_SESSION['rol'] === 'Administrador'): ?>
    $(document).ready(function() {
        $('#usuarios').DataTable({
            // Opciones de DataTables
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            },
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [5, 10, 25, 50, 100, 200]
        });

        // Hacer las filas clicables
        $('#usuarios tbody').on('click', 'tr', function() {
            var href = $(this).data('href');
            if(href) {
                window.location.href = href;
            }
        });
    });
    <?php endif; ?>
</script>
<?php include('../inc/menu-foot.php'); ?>
<?php include('../inc/footer.php'); ?>
</body>
</html>
