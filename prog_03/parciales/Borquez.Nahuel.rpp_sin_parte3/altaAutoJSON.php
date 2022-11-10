<?php

require_once "./clases/auto.php";

use Borquez\Nahuel\Auto;

$patente = isset($_POST["patente"]) ? $_POST["patente"] : NULL;
$marca = isset($_POST["marca"]) ?  $_POST["marca"] : NULL;
$color = isset($_POST["color"]) ? $_POST["color"] : NULL;
$precio = isset($_POST["precio"]) ? (float)$_POST["precio"] : NULL;

$auto = new Auto($patente, $marca, $color, $precio);

echo $auto->guardarJSON('./archivos/autos.json');

?>