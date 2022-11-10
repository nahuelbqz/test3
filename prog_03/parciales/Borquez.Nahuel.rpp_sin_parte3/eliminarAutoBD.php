<?php

require_once "./clases/autoBD.php";

$auto_json = isset($_POST["auto_json"]) ? $_POST["auto_json"] : NULL;
$objJson = json_decode($auto_json);

$auto = new AutoBD($objJson->patente, $objJson->marca, $objJson->color, $objJson->precio);

$rta = new stdClass();

if($auto->eliminar($auto->getPatente()))
{
    $auto->guardarJSON('./archivos/autos_eliminados.json');

    $rta->exito = true;
    $rta->mensaje = "SE elimino de la BD";
}
else
{
    $rta->exito = false;
    $rta->mensaje = "NO se pudo eliminar de la BD!!";
}

echo json_encode($rta);

?>