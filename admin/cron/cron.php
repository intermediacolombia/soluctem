<?php
date_default_timezone_set('America/Bogota');


$hora = date('H:i');

switch ($hora) {
    case '00:00':                 // tareas de medianoche         
        include 'bk.php';          // Backup BD
        break;

    case '12:00':                 // 12:00 → Tareas Medio Dia
        include 'bk.php';
        break;    

    default:
        echo "$hora No es la hora programada.";
}

?>