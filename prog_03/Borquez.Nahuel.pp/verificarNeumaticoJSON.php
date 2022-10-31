<?php

require_once("./clases/neumatico.php");

use Borquez\Nahuel\Neumatico;

$marca = isset($_POST["marca"]) ?  $_POST["marca"] : NULL;
$medidas = isset($_POST["medidas"]) ?  $_POST["medidas"] : NULL;

$miNeumatico = new Neumatico($marca, $medidas, 0);

echo Neumatico::verificarNeumaticoJSON($miNeumatico);

?>