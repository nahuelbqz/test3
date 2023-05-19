<?php

require_once("./clases/accesoDatos.php");
require_once("./clases/neumaticoBD.php");

use Borquez\Nahuel\NeumaticoBD;


$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;

$retorno = new stdClass();
$retorno->exito = false;
$retorno->mensaje = "No se pudo agregar el neumatico";

if($neumatico_json) 
{
    $obj = json_decode($neumatico_json, true);

    $neumaticoBD = new NeumaticoBD($obj["marca"], $obj["medidas"], $obj["precio"], 0, "");


    if($neumaticoBD->agregar()) {
        $retorno->exito = true;
        $retorno->mensaje = "Neumatico agregado";
    }

}

echo json_encode($retorno);

?>

