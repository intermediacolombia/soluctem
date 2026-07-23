<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Archivos por Rango de Fechas</title>
</head>
<body>
    <h2>Eliminar archivos por fecha de modificación</h2>
    <form action="eliminar_archivos.php" method="POST">
        <label for="inicio">Desde:</label>
        <input type="date" id="inicio" name="inicio" required><br><br>

        <label for="fin">Hasta:</label>
        <input type="date" id="fin" name="fin" required><br><br>

        <button type="submit">Eliminar Archivos</button>
    </form>
</body>
</html>
