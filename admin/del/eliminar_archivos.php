<?php
// Ruta del directorio a limpiar (modifica esta)
$directorio = '/home/soluctem/sistema.soluctem.com.co/uploads';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fechaInicio = $_POST['inicio'];
    $fechaFin = $_POST['fin'];

    // Validación básica
    if (!$fechaInicio || !$fechaFin) {
        die("Debes seleccionar ambas fechas.");
    }

    $inicio = strtotime($fechaInicio . ' 00:00:00');
    $fin    = strtotime($fechaFin . ' 23:59:59');

    if ($inicio > $fin) {
        die("La fecha de inicio no puede ser mayor que la fecha final.");
    }

    $archivosEliminados = 0;

    foreach (scandir($directorio) as $archivo) {
        $ruta = $directorio . '/' . $archivo;

        if (is_file($ruta)) {
            $modificado = filemtime($ruta);

            if ($modificado >= $inicio && $modificado <= $fin) {
                if (unlink($ruta)) {
                    echo "✅ Eliminado: $archivo<br>";
                    $archivosEliminados++;
                } else {
                    echo "❌ No se pudo eliminar: $archivo<br>";
                }
            }
        }
    }

    echo "<hr><strong>Total eliminados: $archivosEliminados</strong>";
} else {
    echo "Acceso no permitido.";
}
?>
