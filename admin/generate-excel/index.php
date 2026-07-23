<?php include('../login/sesion.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Reporte en Excel</title>
    <?php include('../inc/header.php'); ?>
    <!-- Incluir Bootstrap CSS para estilos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluir FontAwesome para iconos -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f7f7f7;
        }
        .container {
            margin-top: 100px;
        }
        .card {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.1);
            background-color: #fff;
        }
    </style>
</head>
<body>
    <?php include('../inc/menu.php'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <h3 class="text-center mb-4">Generar Reporte en Excel</h3>
				<center><img src="https://media1.giphy.com/media/v1.Y2lkPTc5MGI3NjExbzRzMGl1OXNlbnV4Zzd6N2s2bHhyZDZoMGloZzFrN3hhYjVzY2treiZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/9i7dTHKDOGAUmZI8PC/giphy.gif" width="150px"></center>

                <!-- Mostrar mensaje de error si no se encontraron registros -->
                <?php
                session_start();
                if (isset($_SESSION['no_data'])) {
                    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">'
                        . htmlspecialchars($_SESSION['no_data']) . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
                    unset($_SESSION['no_data']);
                }
                ?>

                <!-- Mostrar mensaje de éxito al generar el reporte -->
                <?php
                if (isset($_SESSION['excel_success'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
                        . htmlspecialchars($_SESSION['excel_success']) . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
                    unset($_SESSION['excel_success']);
                }
                ?>

                <form action="excel.php" method="GET">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">Fecha de Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" max="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fa fa-file-excel-o"></i> Generar Reporte
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Incluir Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include('../inc/menu-foot.php'); ?>
<?php include('../inc/footer.php'); ?>
</body>
</html>
