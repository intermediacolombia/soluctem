<?php
include('../conexion/conexion_db.php');
$alistamiento=$_GET['alistamiento'];
$alistamiento="$alistamiento";
?>


<html>
<head>
<meta charset="utf-8">
<title>Alistamiento Diario N° <? echo $alistamiento;?></title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300&display=swap" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Ramabhadra&display=swap" rel="stylesheet">
	<style>
	
		.hoja{
	width: 850px;
	 /*border: 2px #DCDCDC solid;*/
	position: relative;
	overflow:auto;
			
	
	
	
	
	}
		
		
		
	.titulo{
	background: #00A859;
	padding: 5px;
	color: #ffffff;
	border-radius: 5px;
	font-size: 15px;
	font-weight: 700;
	margin-bottom: 15px;
	font-family: 'Noto Sans', sans-serif;
	text-align: center;
	margin-top: 8px;
	margin-bottom: 8px;
}

.respuesta {
	float: right;
	font-size: 14px;
	height: 15px;
	
	
}
		
	

.pregunta{
	float: left;
	font-weight: 700;	
	font-size: 14px;
	height: 15px;
	
}

.items{
	border-bottom:#DCDCDC 1px solid;
	font-family: 'Varela Round', sans-serif;
	overflow:auto;
	height: 18px;
	text-transform: capitalize;
	
}
.comentarios{
	font-family: 'Sora', sans-serif;

}

.logo{
	float: left;
	
}

.logo-super{
	float: right;
	
}

.izquierda{
	float: left;
	
}

		
.derecha{
	
	float:right;
	
}

.titulo-hoja{
	font-family: 'Ramabhadra', sans-serif;
	font-size: 20px;
}

	
	
	</style>
	
	
	<script>
	function redireccionar(){
  window.location.href = "javascript: history.go(-1)";
}
 
setTimeout("redireccionar()", 5000);
	</script>
	
	
</head>
<body onLoad="javascript:print()">
	
<?php
	
$sql = "select 

ID,fecha, horaentrada, horasalida, propietario, conductor, cedula, telefono, funcionario, marca, modelo, placa, ninterno, kilometraje, soat, revtecmec, tarjetaoperacion, licenciaconduccion, segurocontractual, seguroextcontra, pito, limpiabrisas, espejoretrovisorizq, espejoretrovisorder, niveles, cinturonesseguridad, estadosilleteria, dispositivocontrolvelocidad, lucesinternas, estadoventanas, conos, tacos, extintor, extintorvence, botiquin, contadorgps, herramientas, aseointerior, parabrisas, lucesbajas, lucesmedias, lucesaltas, lucesdireccionales, lucesestacionamiento, lucesfreno, aseoexterior, fichasequipaje, llantarepuesto, estadollantas, presionairellantas, nivelaceitemotor, nivelrefrigerante, nivelliquidofrenos, nivelhidraulico, fugaaceitemotor, tensioncorreas, nivelaceitetransmision, liquidolimpiabrisas, filtroshumedossecos, bateria, nivelelectrolito, bornessulfatacion, comentarios 




from datos_basicos WHERE ID = '$alistamiento' ";
$resultado = mysqli_query($db, $sql);

if (!$resultado) {
    echo 'No se pudo ejecutar la consulta: ' . mysql_error();
    exit;
}

while ($row = mysqli_fetch_array($resultado)) {
    echo 
		
    '
	<center>
	<div class="hoja">
	<table border="0" width="100%">
	<tr>
	<td align="left">
	<img src="https://alistamientodiario.com/administracion/images/logo.png" width="200px">
	</td>
	<td align="right">
	<img src="https://alistamientodiario.com/administracion/images/supertransporte.png" width="200px">
	</td></tr>'.
		
		
		'<tr><td colspan="2"><center><div class="titulo-hoja">Alistamiento diario N° '.$row['ID'].' Vehiculo '.$row['ninterno'].' </div>Resolucion 315 06-02-2013 Ministerio de Transporte</center></td></tr>'.
		
		
		
		
		'<tr><td>'.
		
		
		'<div class="titulo">DATOS BASICOS</div>'.
		
		"<div class='items'><div class='pregunta'>Fecha:</div><div class='respuesta'>".$row['fecha']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Hora de Entrada:</div><div class='respuesta'>".$row['horaentrada']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Hora Salida:</div><div class='respuesta'>".$row['horasalida']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Propietario:</div><div class='respuesta'>".$row['propietario']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Conductor:</div><div class='respuesta'>".$row['conductor']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Cedula:</div><div class='respuesta'>".$row['cedula']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Telefono:</div><div class='respuesta'>".$row['telefono']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Funcionario:</div><div class='respuesta'>".$row['funcionario']."</div></div>".
		
				
		'<div class="titulo">INFORMACION DEL VEHICULO</div>'.
		
		"<div class='items'><div class='pregunta'>Marca:</div><div class='respuesta'>".$row['marca']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Modelo:</div><div class='respuesta'>".$row['modelo']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Placa:</div><div class='respuesta'>".$row['placa']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Numero Interno:</div><div class='respuesta'>".$row['ninterno']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Kilometraje:</div><div class='respuesta'>".$row['kilometraje']."</div></div>".
				
		'<div class="titulo">VENCIMIENTOS</div>'.
		
		
		
		
		"<div class='items'><div class='pregunta'>SOAT:</div><div class='respuesta'>".$row['soat']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Revisión Tecnicomecanica:</div><div class='respuesta'>".$row['revtecmec']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Tarjeta de Operación:</div><div class='respuesta'>".$row['tarjetaoperacion']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Licencia de Conducción:</div><div class='respuesta'>".$row['licenciaconduccion']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Seguro Contractual:</div><div class='respuesta'>".$row['segurocontractual']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Seguro Extra Contractual:</div><div class='respuesta'>".$row['seguroextcontra']."</div></div>".
		
		
		
		'<div class="titulo">INTERIOR DEL VEHICULO</div>'.
		
		
		"<div class='items'><div class='pregunta'>Pito:</div><div class='respuesta'>".$row['pito']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Limpia Brisas:</div><div class='respuesta'>".$row['limpiabrisas']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Espejo Retrovisor Izquierda:</div><div class='respuesta'>".$row['espejoretrovisorizq']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Espejo Retrovisor Derecha:</div><div class='respuesta'>".$row['espejoretrovisorder']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Niveles:</div><div class='respuesta'>".$row['niveles']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Cinturones de Seguridad:</div><div class='respuesta'>".$row['cinturonesseguridad']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Estado Silleteria:</div><div class='respuesta'>".$row['estadosilleteria']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Dispositivo Control de Velocidad:</div><div class='respuesta'>".$row['dispositivocontrolvelocidad']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Luces Internas:</div><div class='respuesta'>".$row['lucesinternas']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Estado ventanas y salidas de emergencia:</div><div class='respuesta'>".$row['estadoventanas']."</div></div>".		
		
		"<div class='items'><div class='pregunta'>Conos:</div><div class='respuesta'>".$row['conos']."</div></div>".
					
				
		"<div class='items'><div class='pregunta'>Tacos:</div><div class='respuesta'>".$row['tacos']."</div></div>".
		
				
		'</td><td>'.
		
		
		
		"<div class='items'><div class='pregunta'>Extintor:</div><div class='respuesta'>".$row['extintor']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Vencimiento Extintor:</div><div class='respuesta'>".$row['extintorvence']."</div></div>".
						
		"<div class='items'><div class='pregunta'>Botiquin:</div><div class='respuesta'>".$row['botiquin']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Contador y GPS:</div><div class='respuesta'>".$row['contadorgps']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Herramientas:</div><div class='respuesta'>".$row['herramientas']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Aseo Interior:</div><div class='respuesta'>".$row['aseointerior']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Parabrisas:</div><div class='respuesta'>".$row['parabrisas']."</div></div>".
		
		
		
		'<div class="titulo">EXTERIOR DEL VEHICULO</div>'.
		
		"<div class='items'><div class='pregunta'>Luces Bajas:</div><div class='respuesta'>".$row['lucesbajas']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Luces Medias:</div><div class='respuesta'>".$row['lucesmedias']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Luces Altas:</div><div class='respuesta'>".$row['lucesaltas']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Luces Direccionales:</div><div class='respuesta'>".$row['lucesdireccionales']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Luces de Estacionamiento:</div><div class='respuesta'>".$row['lucesestacionamiento']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Luces Freno:</div><div class='respuesta'>".$row['lucesfreno']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Aseo Exterior:</div><div class='respuesta'>".$row['aseoexterior']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Fichas Equipaje y Canastilla:</div><div class='respuesta'>".$row['fichasequipaje']."</div></div>".
		
		
		
		'<div class="titulo">LLANTAS</div>'.
		
		
		"<div class='items'><div class='pregunta'>Llanta de Repuesto:</div><div class='respuesta'>".$row['llantarepuesto']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Estado Llantas:</div><div class='respuesta'>".$row['estadollantas']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Presion de Aire:</div><div class='respuesta'>".$row['presionairellantas']."</div></div>".
		
		
		
		
		
		'<div class="titulo">COMPORTAMIENTO DEL MOTOR</div>'.
		
		
		"<div class='items'><div class='pregunta'>Nivel Aceite Motor:</div><div class='respuesta'>".$row['nivelaceitemotor']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Nivel Fluido Refregerante:</div><div class='respuesta'>".$row['nivelrefrigerante']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Nivel liquido de frenos:</div><div class='respuesta'>".$row['nivelliquidofrenos']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Nivel Fluido Hidraulico:</div><div class='respuesta'>".$row['nivelhidraulico']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Fugas Aceite de Motor:</div><div class='respuesta'>".$row['fugaaceitemotor']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Tension Correas:</div><div class='respuesta'>".$row['tensioncorreas']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Nivel Aceite Transmision:</div><div class='respuesta'>".$row['nivelaceitetransmision']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Nivel Liquido Limpiabrisas:</div><div class='respuesta'>".$row['liquidolimpiabrisas']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Filtros Humedos y Secos:</div><div class='respuesta'>".$row['filtroshumedossecos']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Bateria:</div><div class='respuesta'>".$row['bateria']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Nivel Electrolito:</div><div class='respuesta'>".$row['nivelelectrolito']."</div></div>".
		
		"<div class='items'><div class='pregunta'>Ajuste Bornes y Sulfatación:</div><div class='respuesta'>".$row['bornessulfatacion']."</div></div>".
		
					
		'<div class="titulo">OBSERVACIONES</div>'.
		
		"<div class='comentarios'>".$row['comentarios']."</div>".
		
		
		
		'</td>'.
		'</tr></table>'.
		'</div>'.
		
		"</center>";
}

?>

	
	
	</body>
</html>


