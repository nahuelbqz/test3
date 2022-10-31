<?php

require_once "./clases/neumaticoBD.php";

$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : '';
$obj = json_decode($neumatico_json);

$neumatico = new NeumaticoBD($obj->marca, $obj->medidas, $obj->precio, $obj->id);

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