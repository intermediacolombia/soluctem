<?php

// Obtener y sanitizar el parámetro 'id'
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

if (!$id) {
    die("Error: Parámetro 'id' es requerido.");
}

use Dompdf\Dompdf;
use Dompdf\Options;

require_once 'dompdf/autoload.inc.php';

// Configurar las opciones de DomPDF
$options = new Options();
$options->set('isRemoteEnabled', true); // Permitir la carga de contenido remoto (imágenes, CSS, etc.)
$options->set('isHtml5ParserEnabled', true); // Habilitar el soporte para HTML5
$options->set('isPhpEnabled', true); // Permitir la ejecución de PHP dentro del HTML

$pdf = new Dompdf($options);

// Construir la URL para obtener el contenido HTML
$url = "https://" . $_SERVER["HTTP_HOST"] . "/admin/pdf/test3.php?id=" . urlencode($id);
$html = file_get_contents($url);

if ($html === false) {
    die("Error: No se pudo cargar el contenido HTML desde la URL.");
}

$pdf->loadHtml($html);

// Establecer el papel en tamaño A4 o letter y orientación vertical
$pdf->setPaper('letter', 'portrait');
// Forzar márgenes personalizados
$pdf->set_option('defaultPaperMargins', array(20, 15, 20, 15)); // Margen superior, derecho, inferior, izquierdo
// Renderizar el PDF
$pdf->render();

// Generar el archivo PDF
$filename = "FormularioMantenimientoNo" . $id . ".pdf";
$pdf->stream($filename);
?>



