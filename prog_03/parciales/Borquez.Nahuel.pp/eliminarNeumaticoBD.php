<?php

require_once "./clases/neumaticoBD.php";

$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : '';
$obj = json_decode($neumatico_json);

$neumatico = new NeumaticoBD($obj->marca, $obj->medidas, $obj->precio, $obj->id);

$rta = new stdClass();

if($neumatico->eliminar($neumatico->getId()))
{
    $neumatico->guardarJSON('./archivos/neumaticos_eliminados.json');

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