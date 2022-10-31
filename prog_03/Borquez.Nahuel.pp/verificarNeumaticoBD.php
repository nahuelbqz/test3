<?php

require_once "./clases/neumaticoBD.php";

$obj_neumatico = isset($_POST["obj_neumatico"]) ? $_POST["obj_neumatico"] : "";
$obj = json_decode($obj_neumatico);

$neumatico = new NeumaticoBD($obj->marca, $obj->medidas);

$arrayNeumaticos = NeumaticoBD::traer();

if($neumatico->existe($arrayNeumaticos))
{
    return $neumatico->toJSON();
    echo $neumatico->toJSON();
}
else
{
    return "{}";
    echo "{}";
}

?>