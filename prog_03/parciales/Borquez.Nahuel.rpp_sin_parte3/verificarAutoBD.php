<?php

require_once "./clases/autoBD.php";

$obj_auto = isset($_POST["obj_auto"]) ? $_POST["obj_auto"] : NULL;
$objJson = json_decode($obj_auto);

$auto = new AutoBD($objJson->patente);

$arrayAutosBD = AutoBD::traer();

if($auto->existe($arrayAutosBD))
{
    echo $auto->toJSON();
    return $auto->toJSON();
}
else
{
    echo "{}";
    return "{}";
}

?>