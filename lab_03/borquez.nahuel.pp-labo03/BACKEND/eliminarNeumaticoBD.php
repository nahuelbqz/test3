<?php
require_once("./clases/accesoDatos.php");
require_once("./clases/neumaticoBD.php");

use Borquez\Nahuel\NeumaticoBD;

$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;

$retorno = new stdClass();
$retorno->exito = false;
$retorno->mensaje = "No se pudo borrar el neumatico";

if($neumatico_json) 
{
    $obj = json_decode($neumatico_json, true);

    $neumatico = new NeumaticoBD($obj["marca"], $obj["medidas"], $obj["precio"], $obj["id"], "");

    if(NeumaticoBD::eliminar($neumatico->getId()))
    {
        $retorno->exito = true;
        $retorno->mensaje = "Neumatico Eliminado";
        $neumatico->guardarJSON("./archivos/neumaticos_eliminados.json");
    }

}

echo json_encode($retorno);
