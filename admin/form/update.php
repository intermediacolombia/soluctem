<?php
session_start(); // Iniciar la sesión para almacenar mensajes

// Conexión a la base de datos
include('../inc/config.php');
$redirect_params = array();
$allowed_redirect_params = array(
    'filterFechaInicio',
    'filterFechaFin',
    'filterTienda',
    'filterTecnico',
    'filterEstado',
    'search'
);

foreach ($allowed_redirect_params as $param) {
    if (isset($_GET[$param]) && $_GET[$param] !== '') {
        $redirect_params[$param] = $_GET[$param];
    }
}

$formulario_id = isset($_POST['formulario_id']) ? intval($_POST['formulario_id']) : 0;
$redirect_url = "/admin/form/?" . http_build_query(array_merge(array('id' => $formulario_id), $redirect_params));

$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conexion->connect_error) {
    $_SESSION['update_error'] = "Conexión fallida: " . $conexion->connect_error;
    header("Location: $redirect_url");
    exit();
}

// Obtener los datos enviados desde el formulario

// Escapar texto para evitar errores de sintaxis SQL y prevenir inyección
$nombreSolicitante = $conexion->real_escape_string($_POST['nombreSolicitante']);
$cargo = $conexion->real_escape_string($_POST['cargo']);
$nombreTienda = $conexion->real_escape_string($_POST['nombreTienda']);
$numeroTienda = $conexion->real_escape_string($_POST['numeroTienda']);
$numeroTicket = $conexion->real_escape_string($_POST['numeroTicket']);
$fecha = $conexion->real_escape_string($_POST['fecha']);
$municipio = $conexion->real_escape_string($_POST['municipio']);
$departamento = $conexion->real_escape_string($_POST['departamento']);
$nombreEquipo = $conexion->real_escape_string($_POST['nombreEquipo']);
$marca = $conexion->real_escape_string($_POST['marca']);
$serial = $conexion->real_escape_string($_POST['serial']);
$descripcionFalla = $conexion->real_escape_string($_POST['descripcionFalla']);
$diagnosticoTecnico = $conexion->real_escape_string($_POST['diagnosticoTecnico']);
$repuestosCambiados = $conexion->real_escape_string($_POST['repuestosCambiados']);
$observaciones = $conexion->real_escape_string($_POST['observaciones']);

// Checkboxes de tipo de asistencia
$asistenciaReparacion = isset($_POST['asistenciaReparacion']) ? 1 : 0;
$asistenciaGarantia = isset($_POST['asistenciaGarantia']) ? 1 : 0;
$asistenciaAjuste = isset($_POST['asistenciaAjuste']) ? 1 : 0;
$asistenciaModificacion = isset($_POST['asistenciaModificacion']) ? 1 : 0;
$asistenciaServicio = isset($_POST['asistenciaServicio']) ? 1 : 0;
$asistenciaMejora = isset($_POST['asistenciaMejora']) ? 1 : 0;
$asistenciaCombinacion = isset($_POST['asistenciaCombinacion']) ? 1 : 0;

// Checkboxes de tipo/causa de fallas básicas
$fallaOperacion = isset($_POST['fallaOperacion']) ? 1 : 0;
$fallaMecanica = isset($_POST['fallaMecanica']) ? 1 : 0;
$fallaElectrica = isset($_POST['fallaElectrica']) ? 1 : 0;
$fallaTerceros = isset($_POST['fallaTerceros']) ? 1 : 0;
$fallaFabricacion = isset($_POST['fallaFabricacion']) ? 1 : 0;

// Evaluación del servicio (texto)
$seguridadRiesgoAccidentalidad = $conexion->real_escape_string($_POST['seguridadRiesgoAccidentalidad']);
$seguridadRiesgoEquipo = $conexion->real_escape_string($_POST['seguridadRiesgoEquipo']);
$funcionamientoFallaSolucionada = $conexion->real_escape_string($_POST['funcionamientoFallaSolucionada']);
$funcionamientoPasosNormales = $conexion->real_escape_string($_POST['funcionamientoPasosNormales']);
$calidadTrabajo = $conexion->real_escape_string($_POST['calidadTrabajo']);
$limpiezaOrganizacionArmado = $conexion->real_escape_string($_POST['limpiezaOrganizacionArmado']);
$limpiezaOrganizacionAseado = $conexion->real_escape_string($_POST['limpiezaOrganizacionAseado']);
$capacitacionCausa = $conexion->real_escape_string($_POST['capacitacionCausa']);
$capacitacionPrevencion = $conexion->real_escape_string($_POST['capacitacionPrevencion']);
$capacitacionAccion = $conexion->real_escape_string($_POST['capacitacionAccion']);

// Información de los contratistas (texto)
$contratista1 = $conexion->real_escape_string($_POST['contratista1']);
$cedula1 = $conexion->real_escape_string($_POST['cedula1']);
$horaEntrada1 = $conexion->real_escape_string($_POST['horaEntrada1']);
$horaSalida1 = $conexion->real_escape_string($_POST['horaSalida1']);
$contratista2 = $conexion->real_escape_string($_POST['contratista2']);
$cedula2 = $conexion->real_escape_string($_POST['cedula2']);
$horaEntrada2 = $conexion->real_escape_string($_POST['horaEntrada2']);
$horaSalida2 = $conexion->real_escape_string($_POST['horaSalida2']);
$contratista3 = $conexion->real_escape_string($_POST['contratista3']);
$cedula3 = $conexion->real_escape_string($_POST['cedula3']);
$horaEntrada3 = $conexion->real_escape_string($_POST['horaEntrada3']);
$horaSalida3 = $conexion->real_escape_string($_POST['horaSalida3']);
$contratista4 = $conexion->real_escape_string($_POST['contratista4']);
$cedula4 = $conexion->real_escape_string($_POST['cedula4']);
$horaEntrada4 = $conexion->real_escape_string($_POST['horaEntrada4']);
$horaSalida4 = $conexion->real_escape_string($_POST['horaSalida4']);

// Funcionario (texto)
$nombreFuncionario = $conexion->real_escape_string($_POST['nombreFuncionario']);
$cedulaFuncionario = $conexion->real_escape_string($_POST['cedulaFuncionario']);
$cargoFuncionario = $conexion->real_escape_string($_POST['cargoFuncionario']);
$sapFuncionario = $conexion->real_escape_string($_POST['sapFuncionario']);


// Preparar la consulta de actualización
$sql = "UPDATE formulario SET 
    nombreSolicitante = '$nombreSolicitante', 
    cargo = '$cargo',
    nombreTienda = '$nombreTienda',
    numeroTienda = '$numeroTienda',
    numeroTicket = '$numeroTicket',
    fecha = '$fecha',
    municipio = '$municipio',
    departamento = '$departamento',
    nombreEquipo = '$nombreEquipo',
    marca = '$marca',
    serial = '$serial',
    descripcionFalla = '$descripcionFalla',
    diagnosticoTecnico = '$diagnosticoTecnico',
    repuestosCambiados = '$repuestosCambiados',
    observaciones = '$observaciones',
    asistenciaReparacion = $asistenciaReparacion,
    asistenciaGarantia = $asistenciaGarantia,
    asistenciaAjuste = $asistenciaAjuste,
    asistenciaModificacion = $asistenciaModificacion,
    asistenciaServicio = $asistenciaServicio,
    asistenciaMejora = $asistenciaMejora,
    asistenciaCombinacion = $asistenciaCombinacion,
    fallaOperacion = $fallaOperacion,
    fallaMecanica = $fallaMecanica,
    fallaElectrica = $fallaElectrica,
    fallaTerceros = $fallaTerceros,
    fallaFabricacion = $fallaFabricacion,
    seguridadRiesgoAccidentalidad = '$seguridadRiesgoAccidentalidad',
    seguridadRiesgoEquipo = '$seguridadRiesgoEquipo',
    funcionamientoFallaSolucionada = '$funcionamientoFallaSolucionada',
    funcionamientoPasosNormales = '$funcionamientoPasosNormales',
    calidadTrabajo = '$calidadTrabajo',
    limpiezaOrganizacionArmado = '$limpiezaOrganizacionArmado',
    limpiezaOrganizacionAseado = '$limpiezaOrganizacionAseado',
    capacitacionCausa = '$capacitacionCausa',
    capacitacionPrevencion = '$capacitacionPrevencion',
    capacitacionAccion = '$capacitacionAccion',
    contratista1 = '$contratista1',
    cedula1 = '$cedula1',
    horaEntrada1 = '$horaEntrada1',
    horaSalida1 = '$horaSalida1',
    contratista2 = '$contratista2',
    cedula2 = '$cedula2',
    horaEntrada2 = '$horaEntrada2',
    horaSalida2 = '$horaSalida2',
    contratista3 = '$contratista3',
    cedula3 = '$cedula3',
    horaEntrada3 = '$horaEntrada3',
    horaSalida3 = '$horaSalida3',
    contratista4 = '$contratista4',
    cedula4 = '$cedula4',
    horaEntrada4 = '$horaEntrada4',
    horaSalida4 = '$horaSalida4',
    nombreFuncionario = '$nombreFuncionario',
    cedulaFuncionario = '$cedulaFuncionario',
    cargoFuncionario = '$cargoFuncionario',
    sapFuncionario = '$sapFuncionario'
WHERE id = $formulario_id";

// Ejecutar la consulta
if ($conexion->query($sql) === TRUE) {
    $_SESSION['update_success'] = "Formulario actualizado correctamente.";
} else {
    $_SESSION['update_error'] = "Error actualizando el formulario: " . $conexion->error;
}

// Manejo de imágenes
$uploadDir = $ruta_base . '/uploads/'; // Directorio donde guardar las imágenes en el servidor
$relativeDir = '/uploads/'; // Ruta relativa que se guardará en la base de datos

try {
    if (!empty($_FILES['nuevas_imagenes']['name'][0])) {
        foreach ($_FILES['nuevas_imagenes']['name'] as $key => $name) {
            $tmpName = $_FILES['nuevas_imagenes']['tmp_name'][$key];

            // Generar un nombre único para la imagen
            $timestamp = round(microtime(true) * 1000);
            $randomNumber = mt_rand(1, 1000);
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $newFileName = "image_" . $timestamp . "_" . $randomNumber . "." . $extension;

            $uploadFile = $uploadDir . $newFileName;

            // Validar que sea una imagen
            $esImagenValida = getimagesize($tmpName);
            if ($esImagenValida === false) {
                throw new Exception("Archivo no válido como imagen: $name");
            }

            // Comprimir según tipo de imagen
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $img = imagecreatefromjpeg($tmpName);
                    if (!$img) throw new Exception("No se pudo procesar imagen JPG: $name");
                    imagejpeg($img, $uploadFile, 70); // 70% calidad
                    imagedestroy($img);
                    break;

                case 'png':
                    $img = imagecreatefrompng($tmpName);
                    if (!$img) throw new Exception("No se pudo procesar imagen PNG: $name");
                    imagepng($img, $uploadFile, 6); // Nivel de compresión 0-9
                    imagedestroy($img);
                    break;

                case 'webp':
                    $img = imagecreatefromwebp($tmpName);
                    if (!$img) throw new Exception("No se pudo procesar imagen WEBP: $name");
                    imagewebp($img, $uploadFile, 75);
                    imagedestroy($img);
                    break;

                default:
                    // Otros formatos se suben sin compresión
                    if (!move_uploaded_file($tmpName, $uploadFile)) {
                        throw new Exception("Error al subir archivo no comprimido: $name");
                    }
                    break;
            }

            // Insertar la ruta relativa en la base de datos
            $imagePath = $conexion->real_escape_string($relativeDir . $newFileName);
            $sqlImage = "INSERT INTO imagenes (formulario_id, imagen) VALUES ('$formulario_id', '$imagePath')";
            if (!$conexion->query($sqlImage)) {
                throw new Exception('Error al guardar la imagen en la base de datos: ' . $conexion->error);
            }
        }
    }

    // Eliminar imágenes seleccionadas
    if (isset($_POST['eliminar_imagenes'])) {
        foreach ($_POST['eliminar_imagenes'] as $img_id) {
            // Obtener la ruta de la imagen
            $sql_img = "SELECT imagen FROM imagenes WHERE id = $img_id";
            $resultado_img = $conexion->query($sql_img);
            $img_data = $resultado_img->fetch_assoc();
            if ($img_data) {
                $img_path = $_SERVER['DOCUMENT_ROOT'] . $img_data['imagen'];
                // Eliminar el archivo físico si existe
                if (file_exists($img_path)) {
                    unlink($img_path);
                }
            }

            // Eliminar el registro de la base de datos
            $sql_delete_img = "DELETE FROM imagenes WHERE id = $img_id";
            $conexion->query($sql_delete_img);
        }
    }

    $_SESSION['update_success'] .= " Imágenes actualizadas correctamente.";

} catch (Exception $e) {
    $_SESSION['update_error'] = $e->getMessage();
}

// Cerrar la conexión y redirigir
$conexion->close();
header("Location: $redirect_url");
exit();
?>


