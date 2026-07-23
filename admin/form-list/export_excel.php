<?php
session_start();
require '../../vendor/autoload.php'; // Ajusta la ruta según tu estructura

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Conexión a la base de datos
include('../inc/config.php');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['zonas'])) {
    die('No autorizado');
}

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");

// Obtener las zonas del usuario
$zonas_usuario = $_SESSION['zonas'];
$zonas_array = explode(',', $zonas_usuario);
$zonas_array = array_map('trim', $zonas_array);

// Preparar las zonas para la consulta
$zonas_escapadas = array();
foreach ($zonas_array as $zona) {
    $zonas_escapadas[] = "'" . $conexion->real_escape_string($zona) . "'";
}
$zonas_para_sql = implode(',', $zonas_escapadas);

// WHERE base
$whereBase = "departamento IN ($zonas_para_sql) AND borrado = 0 AND (nombreSolicitante IS NOT NULL AND nombreSolicitante != '')";

// Filtros personalizados
$customFilters = [];

if (!empty($_GET['filterFechaInicio'])) {
    $fechaInicio = $conexion->real_escape_string($_GET['filterFechaInicio']);
    $customFilters[] = "fecha >= '$fechaInicio'";
}

if (!empty($_GET['filterFechaFin'])) {
    $fechaFin = $conexion->real_escape_string($_GET['filterFechaFin']);
    $customFilters[] = "fecha <= '$fechaFin'";
}

if (!empty($_GET['filterTienda'])) {
    $tienda = $conexion->real_escape_string($_GET['filterTienda']);
    $customFilters[] = "nombreTienda = '$tienda'";
}

if (!empty($_GET['filterTecnico'])) {
    $tecnico = $conexion->real_escape_string($_GET['filterTecnico']);
    $customFilters[] = "nombreSolicitante = '$tecnico'";
}

if (isset($_GET['filterEstado']) && $_GET['filterEstado'] !== '') {
    $estado = intval($_GET['filterEstado']);
    $customFilters[] = "estado = $estado";
}

$whereCustom = "";
if (!empty($customFilters)) {
    $whereCustom = " AND " . implode(' AND ', $customFilters);
}

$whereClause = $whereBase . $whereCustom;

// Obtener datos
$sql = "SELECT id, nombreSolicitante, nombreTienda, numeroTicket, fecha, departamento, estado 
        FROM formulario 
        WHERE $whereClause 
        ORDER BY fecha DESC";

$result = $conexion->query($sql);

// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Configurar propiedades del documento
$spreadsheet->getProperties()
    ->setCreator("Sistema de Formularios")
    ->setTitle("Formularios de Mantenimiento")
    ->setSubject("Exportación de Formularios")
    ->setDescription("Listado de formularios con filtros aplicados");

// Definir encabezados
$encabezados = ['ID', 'Técnico', 'Tienda', 'Número de Ticket', 'Fecha', 'Departamento', 'Estado'];

// Insertar los encabezados en la primera fila usando setCellValue
$columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
for ($i = 0; $i < count($encabezados); $i++) {
    $sheet->setCellValue($columnas[$i] . '1', $encabezados[$i]);
}

// Aplicar estilo al encabezado
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 12
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4472C4']
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
];

$sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

// Ajustar el alto de la fila del encabezado
$sheet->getRowDimension(1)->setRowHeight(25);

// Insertar los datos
if ($result && $result->num_rows > 0) {
    $fila = 2; // Comenzar desde la fila 2
    
    while ($row = $result->fetch_assoc()) {
        $estadoTexto = ($row['estado'] == 1) ? 'Aprobado' : 'Pendiente';
        
        // Insertar datos usando setCellValue
        $sheet->setCellValue('A' . $fila, $row['id']);
        $sheet->setCellValue('B' . $fila, $row['nombreSolicitante']);
        $sheet->setCellValue('C' . $fila, $row['nombreTienda']);
        $sheet->setCellValue('D' . $fila, $row['numeroTicket']);
        $sheet->setCellValue('E' . $fila, $row['fecha']);
        $sheet->setCellValue('F' . $fila, $row['departamento']);
        $sheet->setCellValue('G' . $fila, $estadoTexto);
        
        // Aplicar estilo a la columna de estado
        $estadoCell = 'G' . $fila;
        if ($row['estado'] == 1) {
            // Aprobado - Verde
            $sheet->getStyle($estadoCell)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '155724']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D4EDDA']
                ]
            ]);
        } else {
            // Pendiente - Rojo
            $sheet->getStyle($estadoCell)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '721C24']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8D7DA']
                ]
            ]);
        }
        
        $fila++;
    }
    
    // Aplicar bordes a todas las celdas con datos
    $lastRow = $fila - 1;
    $sheet->getStyle('A1:G' . $lastRow)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'CCCCCC']
            ]
        ]
    ]);
    
    // Aplicar alineación centrada a todas las celdas de datos
    $sheet->getStyle('A2:G' . $lastRow)->applyFromArray([
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ]);
    
} else {
    // Si no hay datos, mostrar un mensaje
    $sheet->setCellValue('A2', 'No se encontraron datos con los filtros aplicados');
    $sheet->mergeCells('A2:G2');
    $sheet->getStyle('A2')->applyFromArray([
        'font' => [
            'italic' => true,
            'color' => ['rgb' => '666666']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER
        ]
    ]);
}

// Ajustar automáticamente el ancho de las columnas
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Establecer encabezados para la descarga
$filename = "formularios_" . date('Y-m-d_His') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1'); // Para IE
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Fecha en el pasado
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: cache, must-revalidate');
header('Pragma: public');

// Guardar el archivo y enviarlo al navegador
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

$conexion->close();
exit;
?>
