<?php

require_once "./clases/autoBD.php";

$auto_json = isset($_POST["auto_json"]) ? $_POST["auto_json"] : NULL;
$objJson = json_decode($auto_json);

$neumatico = new AutoBD($objJson->patente, $objJson->marca, $objJson->color, $objJson->precio);

$rta = new stdClass();

if($neumatico->modificar())
{
    $rta->exito = true;
    $rta->mensaje = "SE modifico correctamente la BD";
}
else
{
    $rta->exito = false;
    $rta->mensaje = "NO se pudo modificar la BD!!";
}

echo json_encode($rta);
?>