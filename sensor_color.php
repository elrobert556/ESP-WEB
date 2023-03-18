<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(0);

require 'conexion.php';
$obj = new BD_PDO();

$Rojo_Frec = $_POST['Rojo_Frec'];
$Verde_Frec = $_POST['Verde_Frec'];
$Azul_Frec = $_POST['Azul_Frec'];
$color = $_POST['color'];
/*date_default_timezone_set('america/matamoros');
$hora = date("Y-m-d H:i:s");*/
if ($Rojo_Frec < 30 && $Verde_Frec > 30 && $Azul_Frec > 25){
    $obj->Ejecutar_Instruccion("Insert into sensor_color (Rojo_Frec,Verde_Frec,Azul_Frec,color) VALUES ('$Rojo_Frec','$Verde_Frec','$Azul_Frec','Blanco')");
}

elseif ($Rojo_Frec > 28 && $Verde_Frec > 40 && $Azul_Frec < 45){
    $obj->Ejecutar_Instruccion("Insert into sensor_color (Rojo_Frec,Verde_Frec,Azul_Frec,color) VALUES ('$Rojo_Frec','$Verde_Frec','$Azul_Frec','Azul')");
}

elseif ($Rojo_Frec == 0 && $Verde_Frec == 0 && $Azul_Frec == 0){
    
}

else{
    $obj->Ejecutar_Instruccion("Insert into sensor_color (Rojo_Frec,Verde_Frec,Azul_Frec,color) VALUES ('$Rojo_Frec','$Verde_Frec','$Azul_Frec','$color')");
}



?>