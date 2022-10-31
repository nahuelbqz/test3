<?php

require_once "./clases/neumatico.php";

use Borquez\Nahuel\Neumatico;

$marca = isset($_POST["marca"]) ?  $_POST["marca"] : NULL;
$medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : NULL;
$precio = isset($_POST["precio"]) ? (float)$_POST["precio"] : NULL;

$miNeumatico = new Neumatico($marca, $medidas, $precio);

echo $miNeumatico->GuardarJSON('./archivos/neumaticos.json');

?>