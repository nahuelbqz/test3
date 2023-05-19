<?php
require_once("./clases/accesoDatos.php");
require_once("./clases/neumaticoBD.php");

use Borquez\Nahuel\NeumaticoBD;

$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;

$retorno = new stdClass();
$retorno->exito = false;
$retorno->mensaje = "No se pudo modificar el neumatico";

if ($neumatico_json) 
{
    $obj = json_decode($neumatico_json, true);

    $neumatico = new NeumaticoBD($obj["marca"], $obj["medidas"], $obj["precio"], $obj["id"]);

    if ($neumatico->modificar()) 
    {
        $retorno->exito = true;
        $retorno->mensaje = "Neumatico modificado";
    }
}

echo json_encode($retorno);
