<?php
require_once "./clases/autoBD.php";

$auto_json = isset($_POST["auto_json"]) ? $_POST["auto_json"] : NULL;
$objJson = json_decode($auto_json);

$auto = new AutoBD($objJson->patente, $objJson->marca, $objJson->color, $objJson->precio,);

$rta = new stdClass();
if($auto->agregar())
{
    $rta->exito = true;
    $rta->mensaje = "Agregado en la BD con exito";
}
else
{
    $rta->exito = false;
    $rta->mensaje = "NO se pudo agregar en la BD!!";
}

echo json_encode($rta);

?>