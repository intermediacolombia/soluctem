<?php
session_start();
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Conectar a la base de datos
include('../inc/config.php');

$conexion = new mysqli($servername, $username, $password, $dbname);
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener las fechas del rango
$fecha_inicio = $_GET['fecha_inicio'] ?? null;
$fecha_fin = $_GET['fecha_fin'] ?? null;

if (!$fecha_inicio || !$fecha_fin) {
    $_SESSION['no_data'] = "No se proporcionaron fechas válidas.";
    header('Location: /admin/generate-excel/');
    exit();
}

// Consulta SQL con contratistas y sus horarios
$sql = "SELECT 
            CONCAT_WS(', ', NULLIF(contratista1, ''), NULLIF(contratista2, ''), NULLIF(contratista3, '')) AS nombre_tecnicos,
            nombreSolicitante, horaEntrada1, horaSalida1, fecha, numeroTienda, 
            nombreTienda, municipio, nombreEquipo, serial, descripcionFalla, 
            diagnosticoTecnico, numeroTicket, observaciones, repuestosCambiados
        FROM formulario 
        WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' 
        ORDER BY fecha ASC";

$resultado = $conexion->query($sql);

if ($resultado->num_rows === 0) {
    $_SESSION['no_data'] = "No se encontraron formularios en el rango de fechas seleccionado.";
    header('Location: /admin/generate-excel/');
    exit();
}

// Crear un nuevo archivo Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("REPORTE");

// Definir encabezados de columnas en mayúsculas
$encabezados = [
    "NOMBRES TÉCNICOS", "NOMBRE SOLICITANTE", "HORA ENTRADA", "HORA DE SALIDA", 
    "FECHA", "NÚMERO DE TIENDA", "NOMBRE DE TIENDA", "CIUDAD", 
    "EQUIPO", "SAP DE EQUIPO", "MOTIVO DEL SERVICIO", "REPORTE TÉCNICO", 
    "TICKET", "OBSERVACIONES", "REPUESTOS CAMBIADOS"
];

// Aplicar estilos al encabezado
$style_encabezado = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'],
        'size' => 12
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'D3D3D3']
    ]
];

// Definir anchos personalizados para cada columna
$anchos = [
    30, 25, 12, 12, 12, // Nombre Técnico, Nombre Solicitante, Hora Entrada, Hora Salida, Fecha
    15, 22, 18, 20, 15, // Número Tienda, Nombre Tienda, Ciudad, Equipo, SAP
    30, 30, 12, 25, 25  // Motivo del Servicio, Reporte Técnico, Ticket, Observaciones, Repuestos Cambiados
];

// Escribir encabezados en la primera fila con estilos
$col = 1;
foreach ($encabezados as $index => $encabezado) {
    $celda = Coordinate::stringFromColumnIndex($col) . '1';
    $sheet->setCellValue($celda, strtoupper($encabezado));
    $sheet->getStyle($celda)->applyFromArray($style_encabezado);
    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setWidth($anchos[$index]);
    $col++;
}

// Escribir datos de la consulta en el Excel
$fila = 2;
while ($row = $resultado->fetch_assoc()) {
    $col = 1;
    
    // Asegurar que los técnicos aparezcan correctamente sin comas adicionales
    $nombre_tecnicos = trim($row['nombre_tecnicos'], ', '); // Elimina comas extra si no hay más técnicos
    
    // Llenar cada celda en la fila con los valores correctos
    $datos = [
        $nombre_tecnicos, $row['nombreSolicitante'], $row['horaEntrada1'], $row['horaSalida1'], $row['fecha'],
        $row['numeroTienda'], $row['nombreTienda'], $row['municipio'], $row['nombreEquipo'], $row['serial'],
        $row['descripcionFalla'], $row['diagnosticoTecnico'], $row['numeroTicket'], $row['observaciones'], $row['repuestosCambiados']
    ];
    
    foreach ($datos as $valor) {
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . $fila, $valor);
        $col++;
    }
    
    $fila++;
}

// Establecer nombre del archivo
$filename = "reporte_contratistas_{$fecha_inicio}_al_{$fecha_fin}.xlsx";

// Configurar cabeceras para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

// Crear y enviar el archivo Excel al navegador
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>