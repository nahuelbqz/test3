<?php
require_once "./clases/neumaticoBD.php";

$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;
$obj = json_decode($neumatico_json);

$neumatico = new NeumaticoBD($obj->marca, $obj->medidas, $obj->precio);

$rta = new stdClass();
if($neumatico->agregar())
{
    $rta->exito = true;
    $rta->mensaje = "Agregado con exito";
}
else
{
    $rta->exito = false;
    $rta->mensaje = "NO se pudo agregar!!";
}

echo json_encode($rta);

?>