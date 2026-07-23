<?php
// Conexión a la base de datos
include('../inc/config.php');

$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener el id del formulario desde la URL
$formulario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($formulario_id > 0) {
    // Consulta SQL para obtener los datos del formulario
    $sql_formulario = "SELECT 
                f.id, 
                f.nombreSolicitante, 
                f.cargo, 
                f.nombreTienda, 
                f.numeroTienda, 
                f.numeroTicket, 
                f.fecha, 
                f.municipio, 
                f.departamento, 
                f.nombreEquipo, 
                f.marca, 
                f.serial, 
                f.descripcionFalla, 
                f.diagnosticoTecnico, 
                f.repuestosCambiados, 
                f.observaciones, 
                f.seguridadRiesgoAccidentalidad, 
                f.seguridadRiesgoEquipo, 
                f.funcionamientoFallaSolucionada, 
                f.funcionamientoPasosNormales, 
                f.calidadTrabajo, 
                f.limpiezaOrganizacionArmado, 
                f.limpiezaOrganizacionAseado, 
                f.capacitacionCausa, 
                f.capacitacionPrevencion, 
                f.capacitacionAccion, 
                f.contratista1, 
                f.cedula1, 
                f.horaEntrada1, 
                f.horaSalida1, 
                f.contratista2, 
                f.cedula2, 
                f.horaEntrada2, 
                f.horaSalida2, 
                f.contratista3, 
                f.cedula3, 
                f.horaEntrada3, 
                f.horaSalida3, 
                f.contratista4, 
                f.cedula4, 
                f.horaEntrada4, 
                f.horaSalida4, 
                f.nombreFuncionario, 
                f.cedulaFuncionario, 
                f.cargoFuncionario, 
                f.sapFuncionario, 
                f.timestamp, 
                f.asistenciaReparacion, 
                f.asistenciaGarantia, 
                f.asistenciaAjuste, 
                f.asistenciaModificacion, 
                f.asistenciaServicio, 
                f.asistenciaMejora, 
                f.asistenciaCombinacion, 
                f.fallaOperacion, 
                f.fallaMecanica, 
                f.fallaElectrica, 
                f.fallaTerceros, 
                f.fallaFabricacion
            FROM formulario f
            WHERE f.id = $formulario_id";

    $resultado_formulario = $conexion->query($sql_formulario);

    if ($resultado_formulario->num_rows > 0) {
        $row = $resultado_formulario->fetch_assoc();

        // Establecer las cabeceras para la descarga del archivo Excel
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=formulario_$formulario_id.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Comenzar a escribir el HTML
        echo '<html>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<body>';

        // Incluir estilos CSS simples
        echo '<style>
                table { border-collapse: collapse; width: 100%; font-size: 10pt; }
                th, td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: top; }
                .section-header { background-color: #d3d3d3; text-align: center; font-weight: bold; font-size: 11pt; }
              </style>';

        // Comenzar la tabla
        echo '<table>';

        // Aquí replicamos la estructura de tu tabla, incluyendo todos los campos

        // SOLICITANTE Y TIENDA BENEFICIARIA
        echo '<tr class="section-header"><td colspan="4">SOLICITANTE Y TIENDA BENEFICIARIA</td></tr>';
        echo '<tr>
                <th>Nombre del Solicitante</th><td>' . htmlspecialchars($row['nombreSolicitante']) . '</td>
                <th>Cargo</th><td>' . htmlspecialchars($row['cargo']) . '</td>
              </tr>';
        echo '<tr>
                <th>Nombre de la Tienda</th><td>' . htmlspecialchars($row['nombreTienda']) . '</td>
                <th>Número de Tienda</th><td>' . htmlspecialchars($row['numeroTienda']) . '</td>
              </tr>';
        echo '<tr>
                <th>Número de Ticket</th><td>' . htmlspecialchars($row['numeroTicket']) . '</td>
                <th>Fecha</th><td>' . htmlspecialchars($row['fecha']) . '</td>
              </tr>';
        echo '<tr>
                <th>Departamento</th><td>' . htmlspecialchars($row['departamento']) . '</td>
                <th>Municipio</th><td>' . htmlspecialchars($row['municipio']) . '</td>
              </tr>';

        // TIPO DE ASISTENCIA
        echo '<tr class="section-header"><td colspan="4">TIPO DE ASISTENCIA</td></tr>';
        echo '<tr>
                <td>Reparación</td><td>' . (($row['asistenciaReparacion'] == 1) ? 'X' : '') . '</td>
                <td>Garantía</td><td>' . (($row['asistenciaGarantia'] == 1) ? 'X' : '') . '</td>
              </tr>';
        echo '<tr>
                <td>Ajuste</td><td>' . (($row['asistenciaAjuste'] == 1) ? 'X' : '') . '</td>
                <td>Modificación</td><td>' . (($row['asistenciaModificacion'] == 1) ? 'X' : '') . '</td>
              </tr>';
        echo '<tr>
                <td>Servicio</td><td>' . (($row['asistenciaServicio'] == 1) ? 'X' : '') . '</td>
                <td>Mejora</td><td>' . (($row['asistenciaMejora'] == 1) ? 'X' : '') . '</td>
              </tr>';
        echo '<tr>
                <td>Combinación</td><td>' . (($row['asistenciaCombinacion'] == 1) ? 'X' : '') . '</td>
                <td></td><td></td>
              </tr>';

        // TIPO/CAUSA DE FALLAS BÁSICAS
        echo '<tr class="section-header"><td colspan="4">TIPO/CAUSA DE FALLAS BÁSICAS</td></tr>';
        echo '<tr>
                <td>Operación</td><td>' . (($row['fallaOperacion'] == 1) ? 'X' : '') . '</td>
                <td>Mecánica</td><td>' . (($row['fallaMecanica'] == 1) ? 'X' : '') . '</td>
              </tr>';
        echo '<tr>
                <td>Eléctrica</td><td>' . (($row['fallaElectrica'] == 1) ? 'X' : '') . '</td>
                <td>Terceros</td><td>' . (($row['fallaTerceros'] == 1) ? 'X' : '') . '</td>
              </tr>';
        echo '<tr>
                <td>Fabricación</td><td>' . (($row['fallaFabricacion'] == 1) ? 'X' : '') . '</td>
                <td></td><td></td>
              </tr>';

        // INFORMACIÓN ÁREA TÉCNICA
        echo '<tr class="section-header"><td colspan="4">INFORMACIÓN ÁREA TÉCNICA</td></tr>';
        echo '<tr>
                <th>Nombre del Equipo</th><td>' . htmlspecialchars($row['nombreEquipo']) . '</td>
                <th>Marca</th><td>' . htmlspecialchars($row['marca']) . '</td>
              </tr>';
        echo '<tr>
                <th>Serial</th><td colspan="3">' . htmlspecialchars($row['serial']) . '</td>
              </tr>';
        echo '<tr>
                <th>Descripción de la Falla</th><td colspan="3">' . htmlspecialchars($row['descripcionFalla']) . '</td>
              </tr>';
        echo '<tr>
                <th>Diagnóstico Técnico</th><td colspan="3">' . htmlspecialchars($row['diagnosticoTecnico']) . '</td>
              </tr>';
        echo '<tr>
                <th>Repuestos Cambiados</th><td colspan="3">' . htmlspecialchars($row['repuestosCambiados']) . '</td>
              </tr>';
        echo '<tr>
                <th>Observaciones</th><td colspan="3">' . htmlspecialchars($row['observaciones']) . '</td>
              </tr>';

        // EVALUACIÓN DEL SERVICIO
        echo '<tr class="section-header"><td colspan="4">EVALUACIÓN DEL SERVICIO</td></tr>';
        echo '<tr>
                <th colspan="3">Descripción</th><th>Cumple/No Cumple</th>
              </tr>';

        // SEGURIDAD
        echo '<tr class="section-header"><td colspan="4">SEGURIDAD</td></tr>';
        echo '<tr>
                <td colspan="3">La labor realizada genera un alto riesgo de accidentalidad para los clientes y/o colaboradores</td>
                <td>' . htmlspecialchars($row['seguridadRiesgoAccidentalidad']) . '</td>
              </tr>';
        echo '<tr>
                <td colspan="3">La labor realizada ofrece algún riesgo para la integridad del equipo</td>
                <td>' . htmlspecialchars($row['seguridadRiesgoEquipo']) . '</td>
              </tr>';

        // FUNCIONALIDAD
        echo '<tr class="section-header"><td colspan="4">FUNCIONALIDAD</td></tr>';
        echo '<tr>
                <td colspan="3">La falla reportada fue solucionada con el trabajo realizado</td>
                <td>' . htmlspecialchars($row['funcionamientoFallaSolucionada']) . '</td>
              </tr>';
        echo '<tr>
                <td colspan="3">Los pasos normales de manejo se siguen sin procedimientos extra</td>
                <td>' . htmlspecialchars($row['funcionamientoPasosNormales']) . '</td>
              </tr>';

        // CALIDAD
        echo '<tr class="section-header"><td colspan="4">CALIDAD</td></tr>';
        echo '<tr>
                <td colspan="3">La calidad del trabajo fue adecuada</td>
                <td>' . htmlspecialchars($row['calidadTrabajo']) . '</td>
              </tr>';

        // LIMPIEZA
        echo '<tr class="section-header"><td colspan="4">LIMPIEZA</td></tr>';
        echo '<tr>
                <td colspan="3">Limpieza - El área intervenida fue dejada organizada y aseada</td>
                <td>' . htmlspecialchars($row['limpiezaOrganizacionArmado']) . '</td>
              </tr>';
        echo '<tr>
                <td colspan="3">Limpieza - Los escombros fueron limpiados</td>
                <td>' . htmlspecialchars($row['limpiezaOrganizacionAseado']) . '</td>
              </tr>';

        // CAPACITACIÓN
        echo '<tr class="section-header"><td colspan="4">CAPACITACIÓN</td></tr>';
        echo '<tr>
                <td colspan="3">Se indicó la causa de la novedad al personal que recibió el trabajo</td>
                <td>' . htmlspecialchars($row['capacitacionCausa']) . '</td>
              </tr>';
        echo '<tr>
                <td colspan="3">Se indicó cómo prevenir que el problema se vuelva a presentar</td>
                <td>' . htmlspecialchars($row['capacitacionPrevencion']) . '</td>
              </tr>';
        echo '<tr>
                <td colspan="3">Se indicó cómo actuar en caso de que el problema se vuelva a presentar</td>
                <td>' . htmlspecialchars($row['capacitacionAccion']) . '</td>
              </tr>';

        // CONSTANCIA DE REALIZACIÓN DE ASISTENCIA
        echo '<tr class="section-header"><td colspan="4">CONSTANCIA DE REALIZACIÓN DE ASISTENCIA</td></tr>';
        echo '<tr>
                <th>Contratista</th><th>Cédula</th><th>Hora Entrada</th><th>Hora Salida</th>
              </tr>';
        echo '<tr>
                <td>' . htmlspecialchars($row['contratista1']) . '</td>
                <td>' . htmlspecialchars($row['cedula1']) . '</td>
                <td>' . htmlspecialchars($row['horaEntrada1']) . '</td>
                <td>' . htmlspecialchars($row['horaSalida1']) . '</td>
              </tr>';
        // Repetimos para contratista2, contratista3, contratista4 si existen
        if (!empty($row['contratista2'])) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['contratista2']) . '</td>
                    <td>' . htmlspecialchars($row['cedula2']) . '</td>
                    <td>' . htmlspecialchars($row['horaEntrada2']) . '</td>
                    <td>' . htmlspecialchars($row['horaSalida2']) . '</td>
                  </tr>';
        }
        if (!empty($row['contratista3'])) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['contratista3']) . '</td>
                    <td>' . htmlspecialchars($row['cedula3']) . '</td>
                    <td>' . htmlspecialchars($row['horaEntrada3']) . '</td>
                    <td>' . htmlspecialchars($row['horaSalida3']) . '</td>
                  </tr>';
        }
        if (!empty($row['contratista4'])) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['contratista4']) . '</td>
                    <td>' . htmlspecialchars($row['cedula4']) . '</td>
                    <td>' . htmlspecialchars($row['horaEntrada4']) . '</td>
                    <td>' . htmlspecialchars($row['horaSalida4']) . '</td>
                  </tr>';
        }

        // FUNCIONARIO
        echo '<tr class="section-header"><td colspan="4">FUNCIONARIO</td></tr>';
        echo '<tr>
                <th>Nombre</th><td>' . htmlspecialchars($row['nombreFuncionario']) . '</td>
                <th>Cédula</th><td>' . htmlspecialchars($row['cedulaFuncionario']) . '</td>
              </tr>';
        echo '<tr>
                <th>Cargo</th><td>' . htmlspecialchars($row['cargoFuncionario']) . '</td>
                <th>SAP</th><td>' . htmlspecialchars($row['sapFuncionario']) . '</td>
              </tr>';

        // Nota: Se han eliminado las secciones de FIRMAS e IMÁGENES ASOCIADAS

        echo '</table>';

        // Finalizar el documento
        echo '</body>';
        echo '</html>';

    } else {
        echo "No se encontraron datos para el ID de formulario proporcionado.";
    }
} else {
    die("ID de formulario no válido.");
}

$conexion->close();
?>

