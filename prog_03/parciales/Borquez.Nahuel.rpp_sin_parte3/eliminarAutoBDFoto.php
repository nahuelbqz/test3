<?php

require_once "./clases/autoBD.php";

$auto_json = isset($_POST["auto_json"]) ? $_POST["auto_json"] : NULL;
$objJson = json_decode($auto_json);

$auto = new AutoBD($objJson->patente, $objJson->marca, $objJson->color, $objJson->precio, $objJson->pathFoto);

////
if(AutoBD::eliminar($auto->getPatente()))
{
    $auto->guardarEnArchivo();

    $jsonRetorno["exito"] = true;
    $jsonRetorno["mensaje"] = "Se elimino el auto con exito.";
}
else
{
    $jsonRetorno["exito"] = false;
    $jsonRetorno["mensaje"] = "Hubo un error al intentar eliminar el auto.";
}

return $jsonRetorno;

?>
