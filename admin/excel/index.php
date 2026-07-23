<?php
require '../../vendor/autoload.php'; // Asegúrate de que la ruta es correcta según tu estructura

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Conexión a la base de datos
include('../inc/config.php');

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener el ID del formulario desde la URL
$formulario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($formulario_id > 0) {
    $sql_formulario = "SELECT * FROM formulario WHERE id = $formulario_id";
    $resultado_formulario = $conexion->query($sql_formulario);

    if ($resultado_formulario->num_rows > 0) {
        $row = $resultado_formulario->fetch_assoc();

        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Definir encabezados
        $encabezados = [
            'ID', 'Nombre del Solicitante', 'Cargo', 'Nombre de Tienda', 'Número de Tienda', 
            'Número de Ticket', 'Fecha', 'Municipio', 'Departamento', 'Nombre del Equipo', 
            'Marca', 'Serial', 'Descripción de la Falla', 'Diagnóstico Técnico', 'Repuestos Cambiados', 
            'Observaciones', 'Seguridad Riesgo Accidentalidad', 'Seguridad Riesgo Equipo', 
            'Funcionamiento Falla Solucionada', 'Funcionamiento Pasos Normales', 'Calidad del Trabajo', 
            'Limpieza Organización Armado', 'Limpieza Organización Aseado', 'Capacitación Causa', 
            'Capacitación Prevención', 'Capacitación Acción', 'Contratista1', 'Cédula1', 'Hora Entrada1', 
            'Hora Salida1', 'Contratista2', 'Cédula2', 'Hora Entrada2', 'Hora Salida2', 'Contratista3', 
            'Cédula3', 'Hora Entrada3', 'Hora Salida3', 'Contratista4', 'Cédula4', 'Hora Entrada4', 
            'Hora Salida4', 'Nombre Funcionario', 'Cédula Funcionario', 'Cargo Funcionario', 'SAP Funcionario'
        ];

        // Insertar los encabezados en la primera fila
        $col = 1;
        foreach ($encabezados as $encabezado) {
            $sheet->setCellValueByColumnAndRow($col, 1, $encabezado);
            $col++;
        }

        // Insertar los datos del formulario
        $valores = [
            $row['id'], $row['nombreSolicitante'], $row['cargo'], $row['nombreTienda'], $row['numeroTienda'], 
            $row['numeroTicket'], $row['fecha'], $row['municipio'], $row['departamento'], $row['nombreEquipo'], 
            $row['marca'], $row['serial'], $row['descripcionFalla'], $row['diagnosticoTecnico'], $row['repuestosCambiados'], 
            $row['observaciones'], $row['seguridadRiesgoAccidentalidad'], $row['seguridadRiesgoEquipo'], 
            $row['funcionamientoFallaSolucionada'], $row['funcionamientoPasosNormales'], $row['calidadTrabajo'], 
            $row['limpiezaOrganizacionArmado'], $row['limpiezaOrganizacionAseado'], $row['capacitacionCausa'], 
            $row['capacitacionPrevencion'], $row['capacitacionAccion'], $row['contratista1'], $row['cedula1'], $row['horaEntrada1'], 
            $row['horaSalida1'], $row['contratista2'], $row['cedula2'], $row['horaEntrada2'], $row['horaSalida2'], $row['contratista3'], 
            $row['cedula3'], $row['horaEntrada3'], $row['horaSalida3'], $row['contratista4'], $row['cedula4'], $row['horaEntrada4'], 
            $row['horaSalida4'], $row['nombreFuncionario'], $row['cedulaFuncionario'], $row['cargoFuncionario'], $row['sapFuncionario']
        ];

        // Insertar datos en la segunda fila
        $col = 1;
        foreach ($valores as $valor) {
            $sheet->setCellValueByColumnAndRow($col, 2, $valor);
            $col++;
        }

        // Establecer encabezados para la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="formulario_' . $formulario_id . '.xlsx"');
        header('Cache-Control: max-age=0');


        // Guardar el archivo y enviarlo al navegador
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } else {
        echo "No se encontraron datos para el ID de formulario proporcionado.";
    }
} else {
    die("ID de formulario no válido.");
}

$conexion->close();
?>
