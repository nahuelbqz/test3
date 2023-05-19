<?php

require_once("./clases/Usuario.php");
use Borquez\Usuario;

$id = isset($_POST["id"]) ?  (int)$_POST["id"] : 0;
//$accion = isset($_POST["accion"]) ? $_POST["accion"] : NULL;

$obj = new stdClass();

if(isset($_POST["accion"]))
{
    if(Usuario::Eliminar($id))
    {
        $obj->exito = true;
        $obj->mensaje = "Usuario ELIMINADO correctamente";

        //var_dump(json_encode($obj));
        echo json_encode($obj);
    }
    else
    {
        $obj->exito = false;
        $obj->mensaje = "ERROR AL ELIMINAR";

        //var_dump(json_encode($obj));
        echo json_encode($obj);
    }
}
else
{
    echo "ERROR";
}

?>