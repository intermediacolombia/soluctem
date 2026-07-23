<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Permitir el encabezado Authorization

// Verificar el token de autorización
$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if ($authHeader !== '5fuUMw9Km7FypJFdlaHoSkQvDl4ijrPsuTkzYc47od3T23CacsODQOI72830kld8xmcTa2TzQVDeXQXlR2jGOoL4IttJO0zpFSWmaSE03zbZL15Op4kXAdIljfEqwLYOHzKIbtttsJMFeHdI2YW7qr2Fu5pZJTRR1XmiKK4y5oZch8d6Si5XSpbCq9HGxhQMZHyq7L1AcvT5gyTQ9Uk5xYgcjhMIA1ZcAoU88p5LuzQKxbk7KU3HOECBz0jJJcrPf55dKaUY35oQKoSU223wFh0vIpvK7WWtLiOAEZDkM6UQT9rjbWhjHqgsbdWk8BJKziEbp2mFs6G54fd4TDXZiEUz6uXiswtcIljSIauCp9Du3VJiWAv3uyfpRhCZ8l5ZjQIEQtQYcLQK16KxDgFWbK6QvpGsOts81OTQA0UhFtyzs9E2ziRx8LGgQYGTSzB5o2ahg2MIDl2u2C0') {
    die(json_encode(['success' => false, 'message' => 'Token de autorización no válido.']));
}

include('../admin/inc/config.php');

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]));
}

// Comenzar la transacción
$conn->begin_transaction();

function comprimirImagen($origen, $destino, $calidad = 75) {
    $info = getimagesize($origen);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $imagen = imagecreatefromjpeg($origen);
            imagejpeg($imagen, $destino, $calidad);
            break;
        case 'image/png':
            $imagen = imagecreatefrompng($origen);
            // PNG usa compresión 0-9 (donde 9 es más comprimido)
            imagepng($imagen, $destino, round((100 - $calidad) / 10));
            break;
        case 'image/webp':
            $imagen = imagecreatefromwebp($origen);
            imagewebp($imagen, $destino, $calidad);
            break;
        default:
            return false; // Tipo no soportado
    }

    imagedestroy($imagen);
    return true;
}




try {
    // Escapar y obtener los datos del formulario
    $nombreSolicitante = $conn->real_escape_string($_POST['nombreSolicitante']);
    $cargo = $conn->real_escape_string($_POST['cargo']);
    $nombreTienda = $conn->real_escape_string($_POST['nombreTienda']);
    $numeroTienda = $conn->real_escape_string($_POST['numeroTienda']);
    $numeroTicket = $conn->real_escape_string($_POST['numeroTicket']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $municipio = $conn->real_escape_string($_POST['municipio']);
    $departamento = $conn->real_escape_string($_POST['departamento']);
    $nombreEquipo = $conn->real_escape_string($_POST['nombreEquipo']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $serial = $conn->real_escape_string($_POST['serial']);
    $descripcionFalla = $conn->real_escape_string($_POST['descripcionFalla']);
    $diagnosticoTecnico = $conn->real_escape_string($_POST['diagnosticoTecnico']);
    $repuestosCambiados = $conn->real_escape_string($_POST['repuestosCambiados']);
    $observaciones = $conn->real_escape_string($_POST['observaciones']);
    $seguridadRiesgoAccidentalidad = $conn->real_escape_string($_POST['seguridadRiesgoAccidentalidad']);
    $seguridadRiesgoEquipo = $conn->real_escape_string($_POST['seguridadRiesgoEquipo']);
    $funcionamientoFallaSolucionada = $conn->real_escape_string($_POST['funcionamientoFallaSolucionada']);
    $funcionamientoPasosNormales = $conn->real_escape_string($_POST['funcionamientoPasosNormales']);
    $calidadTrabajo = $conn->real_escape_string($_POST['calidadTrabajo']);
    $limpiezaOrganizacionArmado = $conn->real_escape_string($_POST['limpiezaOrganizacionArmado']);
    $limpiezaOrganizacionAseado = $conn->real_escape_string($_POST['limpiezaOrganizacionAseado']);
    $capacitacionCausa = $conn->real_escape_string($_POST['capacitacionCausa']);
    $capacitacionPrevencion = $conn->real_escape_string($_POST['capacitacionPrevencion']);
    $capacitacionAccion = $conn->real_escape_string($_POST['capacitacionAccion']);
    $contratista1 = $conn->real_escape_string($_POST['contratista1']);
    $cedula1 = $conn->real_escape_string($_POST['cedula1']);
    $horaEntrada1 = $conn->real_escape_string($_POST['horaEntrada1']);
    $horaSalida1 = $conn->real_escape_string($_POST['horaSalida1']);
    $contratista2 = $conn->real_escape_string($_POST['contratista2']);
    $cedula2 = $conn->real_escape_string($_POST['cedula2']);
    $horaEntrada2 = $conn->real_escape_string($_POST['horaEntrada2']);
    $horaSalida2 = $conn->real_escape_string($_POST['horaSalida2']);
    $contratista3 = $conn->real_escape_string($_POST['contratista3']);
    $cedula3 = $conn->real_escape_string($_POST['cedula3']);
    $horaEntrada3 = $conn->real_escape_string($_POST['horaEntrada3']);
    $horaSalida3 = $conn->real_escape_string($_POST['horaSalida3']);
    $contratista4 = $conn->real_escape_string($_POST['contratista4']);
    $cedula4 = $conn->real_escape_string($_POST['cedula4']);
    $horaEntrada4 = $conn->real_escape_string($_POST['horaEntrada4']);
    $horaSalida4 = $conn->real_escape_string($_POST['horaSalida4']);
    $nombreFuncionario = $conn->real_escape_string($_POST['nombreFuncionario']);
    $cedulaFuncionario = $conn->real_escape_string($_POST['cedulaFuncionario']);
    $cargoFuncionario = $conn->real_escape_string($_POST['cargoFuncionario']);
    $sapFuncionario = $conn->real_escape_string($_POST['sapFuncionario']);
    $timestamp = $conn->real_escape_string($_POST['timestamp']);
    $asistenciaReparacion = $conn->real_escape_string($_POST['asistenciaReparacion']);
    $asistenciaGarantia = $conn->real_escape_string($_POST['asistenciaGarantia']);
    $asistenciaAjuste = $conn->real_escape_string($_POST['asistenciaAjuste']);
    $asistenciaModificacion = $conn->real_escape_string($_POST['asistenciaModificacion']);
    $asistenciaServicio = $conn->real_escape_string($_POST['asistenciaServicio']);
    $asistenciaMejora = $conn->real_escape_string($_POST['asistenciaMejora']);
    $asistenciaCombinacion = $conn->real_escape_string($_POST['asistenciaCombinacion']);
    $fallaOperacion = $conn->real_escape_string($_POST['fallaOperacion']);
    $fallaMecanica = $conn->real_escape_string($_POST['fallaMecanica']);
    $fallaElectrica = $conn->real_escape_string($_POST['fallaElectrica']);
    $fallaTerceros = $conn->real_escape_string($_POST['fallaTerceros']);
    $fallaFabricacion = $conn->real_escape_string($_POST['fallaFabricacion']);
    $firma_digital_cliente = $conn->real_escape_string($_POST['firma_digital_cliente']);
    $firma_digital_tecnico = $conn->real_escape_string($_POST['firma_digital_tecnico']);

    // Insertar datos del formulario
    $sql = "INSERT INTO formulario (
        nombreSolicitante, cargo, nombreTienda, numeroTienda, numeroTicket, fecha, municipio, departamento,
        nombreEquipo, marca, serial, descripcionFalla, diagnosticoTecnico, repuestosCambiados, observaciones,
        seguridadRiesgoAccidentalidad, seguridadRiesgoEquipo, funcionamientoFallaSolucionada, funcionamientoPasosNormales,
        calidadTrabajo, limpiezaOrganizacionArmado, limpiezaOrganizacionAseado, capacitacionCausa,
        capacitacionPrevencion, capacitacionAccion, contratista1, cedula1, horaEntrada1, horaSalida1,
        contratista2, cedula2, horaEntrada2, horaSalida2, contratista3, cedula3, horaEntrada3, horaSalida3,
        contratista4, cedula4, horaEntrada4, horaSalida4, nombreFuncionario, cedulaFuncionario, cargoFuncionario,
        sapFuncionario, timestamp, asistenciaReparacion, asistenciaGarantia, asistenciaAjuste, asistenciaModificacion,
        asistenciaServicio, asistenciaMejora, asistenciaCombinacion, fallaOperacion, fallaMecanica, fallaElectrica,
        fallaTerceros, fallaFabricacion, firma_digital_cliente, firma_digital_tecnico
    ) VALUES (
        '$nombreSolicitante', '$cargo', '$nombreTienda', '$numeroTienda', '$numeroTicket', '$fecha', '$municipio', '$departamento',
        '$nombreEquipo', '$marca', '$serial', '$descripcionFalla', '$diagnosticoTecnico', '$repuestosCambiados', '$observaciones',
        '$seguridadRiesgoAccidentalidad', '$seguridadRiesgoEquipo', '$funcionamientoFallaSolucionada', '$funcionamientoPasosNormales',
        '$calidadTrabajo', '$limpiezaOrganizacionArmado', '$limpiezaOrganizacionAseado', '$capacitacionCausa',
        '$capacitacionPrevencion', '$capacitacionAccion', '$contratista1', '$cedula1', '$horaEntrada1', '$horaSalida1',
        '$contratista2', '$cedula2', '$horaEntrada2', '$horaSalida2', '$contratista3', '$cedula3', '$horaEntrada3', '$horaSalida3',
        '$contratista4', '$cedula4', '$horaEntrada4', '$horaSalida4', '$nombreFuncionario', '$cedulaFuncionario', '$cargoFuncionario',
        '$sapFuncionario', '$timestamp', '$asistenciaReparacion', '$asistenciaGarantia', '$asistenciaAjuste', '$asistenciaModificacion',
        '$asistenciaServicio', '$asistenciaMejora', '$asistenciaCombinacion', '$fallaOperacion', '$fallaMecanica', '$fallaElectrica',
        '$fallaTerceros', '$fallaFabricacion', '$firma_digital_cliente', '$firma_digital_tecnico'
    )";

    if ($conn->query($sql) === TRUE) {
        $formulario_id = $conn->insert_id;

        // Procesar las imágenes cargadas
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = '/home/soluctem/sistema.soluctem.com.co/uploads/'; // Directorio donde guardar las imágenes en el servidor
            $relativeDir = '/uploads/'; // Ruta relativa que se guardará en la base de datos

            foreach ($_FILES['images']['name'] as $key => $name) {
				$tmpName = $_FILES['images']['tmp_name'][$key];
				$fileExtension = pathinfo($name, PATHINFO_EXTENSION);
				$safeFilename = uniqid('img_', true) . '.' . $fileExtension;
				$uploadFile = $uploadDir . $safeFilename;

				// Comprimir antes de mover
				if (comprimirImagen($tmpName, $uploadFile, 75)) {
					$imagePath = $conn->real_escape_string($relativeDir . $safeFilename);
					$sqlImage = "INSERT INTO imagenes (formulario_id, imagen) VALUES ('$formulario_id', '$imagePath')";
					if (!$conn->query($sqlImage)) {
						throw new Exception('Error al guardar la imagen en la base de datos: ' . $conn->error);
					}
				} else {
					throw new Exception('Error al comprimir o guardar la imagen: ' . $name);
				}
			}

        }

        // (Opcional) Hacer un SELECT para verificar qué quedó guardado
        $sqlCheck = "SELECT * FROM formulario WHERE id = '$formulario_id' LIMIT 1";
        $resultCheck = $conn->query($sqlCheck);

        if ($resultCheck && $resultCheck->num_rows > 0) {
            $row = $resultCheck->fetch_assoc();
            // Confirmar la transacción
            $conn->commit();

            // Retornar los datos para compararlos en el cliente si se desea
            echo json_encode([
                'success' => true,
                'message' => 'Formulario enviado exitosamente.',
                'data'    => $row
            ]);
        } else {
            throw new Exception('No se pudo verificar el registro insertado.');
        }

    } else {
        throw new Exception('Error al guardar el formulario: ' . $conn->error);
    }
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Cerrar la conexión a la base de datos
$conn->close();
?>








