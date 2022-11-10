<?php

require_once("./clases/auto.php");

use Borquez\Nahuel\Auto;

$patente = isset($_POST["patente"]) ?  $_POST["patente"] : NULL;

$auto = new Auto($patente, "", "", 0);

echo Auto::verificarAutoJSON($auto);

?>