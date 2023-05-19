<?php

require_once("./clases/Usuario.php");
use Borquez\Usuario;

$usuario_json = isset($_POST["usuario_json"]) ?  $_POST["usuario_json"] : NULL;
$params = json_decode($usuario_json);
//var_dump($params);
$obj = new stdClass();

$usuario = new Usuario();
$usuario->id = $params->id;
$usuario->nombre = $params->nombre;
$usuario->clave = $params->clave;
$usuario->correo = $params->correo;
$usuario->id_perfil = $params->id_perfil;
//var_dump($usuario);die();

if($usuario->Modificar())
{
    $obj->exito = true;
    $obj->mensaje = "Usuario Modificado correctamente";

    //var_dump(json_encode($obj));
    echo json_encode($obj);
}
else
{
    $obj->exito = false;
    $obj->mensaje = "ERROR en la MODIFICACION!!";

    //var_dump(json_encode($obj));
    echo json_encode($obj);
}

//var_dump(json_encode($obj));

?>