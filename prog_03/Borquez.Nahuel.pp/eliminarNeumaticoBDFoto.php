<?php

require_once "./clases/neumaticoBD.php";

$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : "";
$objJson = json_decode($neumatico_json);

$neumatico = new NeumaticoBD($objJson->marca, $objJson->medidas, $objJson->precio, $objJson->id, $objJson->pathFoto);

//dudoso
if(NeumaticoBD::eliminar($neumatico->getId()))
{
    $neumatico->guardarEnArchivo();

    $jsonRetorno["exito"] = true;
    $jsonRetorno["mensaje"] = "Se elimino el neumatico con exito.";
}
else
{
    $jsonRetorno["exito"] = false;
    $jsonRetorno["mensaje"] = "Hubo un error al intentar eliminar el neumatico.";
}

return $jsonRetorno;
?>


