<?php

require_once("./clases/Empleado.php");
use Borquez\Empleado;

$id = isset($_POST["id"]) ?  (int)$_POST["id"] : 0;
$obj = new stdClass();

if(Empleado::EliminarF($id))
{
    $obj->exito = true;
    $obj->mensaje = "Empleado ELIMINADO correctamente.";
    var_dump(json_encode($obj));
}
else
{
    $obj->exito = false;
    $obj->mensaje = "ERROR AL ELIMINAR empleado!!";
    var_dump(json_encode($obj));
}

?>