<?php

require_once("./clases/Usuario.php");
use Borquez\Usuario;

$correo = isset($_POST["correo"]) ?  $_POST["correo"] : NULL;
$clave = isset($_POST["clave"]) ? $_POST["clave"] : NULL;
$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : NULL;
$id_perfil = isset($_POST["id_perfil"]) ? (int)$_POST["id_perfil"] : 0;

$respuesta = new stdClass();

$usuario = new Usuario();
$usuario->nombre = $nombre;
$usuario->clave = $clave;
$usuario->correo = $correo;
$usuario->id_perfil = $id_perfil;

if($usuario->Agregar())
{
    $respuesta->exito = true;
    $respuesta->mensaje = "SE AGREGO EL USUARIO";
}
else
{
    $respuesta->exito = false;
    $respuesta->mensaje = "NO SE HA PODIDO AGREGAR AL USUARIO";
}

var_dump(json_encode($respuesta));

?>