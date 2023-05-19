<?php
require_once("./clases/neumatico.php");
use Borquez\Nahuel\Neumatico;

$marca = isset($_POST["marca"]) ? $_POST["marca"] : NULL;
$medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : NULL;

$retorno = new stdClass();
$retorno->exito = false;
$retorno->mensaje = "El neumatico no existe";

if($marca && $medidas) 
{
    $neumatico = new Neumatico($marca, $medidas);
    $mensaje_json = json_decode(Neumatico::verificarNeumaticoJSON($neumatico), true);

    if($mensaje_json["exito"]) {
        $retorno->exito = true;
        $retorno->mensaje = "El neumatico se encuentra en el listado";
    }
    
}

$retorno->mensaje = $retorno->mensaje . "." . $mensaje_json["mensaje"];

echo json_encode($retorno);