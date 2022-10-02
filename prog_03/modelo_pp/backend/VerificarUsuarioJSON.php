<?php

require_once("./clases/Usuario.php");
use Borquez\Usuario;

$usuario_json = isset($_POST["usuario_json"]) ?  $_POST["usuario_json"] : NULL;
$params = json_decode($usuario_json);
$obj = new stdClass();

if(Usuario::TraerUno($params) != NULL)
{
    $obj->exito = true;
    $obj->mensaje = "Usuario Existente";

    var_dump(json_encode($obj));
}
else
{
    $obj->exito = false;
    $obj->mensaje = "Usuario NO Encontrado";
    
    var_dump(json_encode($obj));
}


?>

