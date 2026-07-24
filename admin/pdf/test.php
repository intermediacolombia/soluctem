<?php include('../inc/config.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formato Único de Soporte - Jerónimo Martins Colombia</title>
    
   <body>
	   

<table border="1" width="100%">
  <tr>
    <th colspan="6">CONTRATISTA</th>
  </tr>
  <tr>
    <th>Razón Social</th>
    <td><?= $razon_social ?></td>
    <th>N° NIT</th>
    <td><?= $nit ?></td>
  </tr>
  <tr>
    <th>Contacto</th>
    <td><?= $contacto ?></td>
    <th>Teléfono</th>
    <td><?= $telefono ?></td>
  </tr>
	 <tr>
    <th colspan="6">SOLICITANTE Y TIENDA BENEFICIARIA</th>
  </tr>
	<tr>
	<th>Nombre del solictante:</th><td>hola</td><th>Cargo</th><td>hola</td>	
	</tr>
	<tr>
		<th>Nombre de la Tienda</th><td>Hola</td><th>No Tienda</th><td>hola</td><th>No Tienda</th><td>Hola</td>
	</tr>
	<th>Municipio:</th><tr>Armenia</tr><th>Departamento:</th><tr>Quindio</tr>
</table>



	   
</body>
</html>