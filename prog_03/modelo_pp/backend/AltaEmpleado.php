<?php

require_once("./clases/Empleado.php");
use Borquez\Empleado;

$correo = isset($_POST["correo"]) ?  $_POST["correo"] : NULL;
$clave = isset($_POST["clave"]) ? $_POST["clave"] : NULL;
$nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : NULL;
$id_perfil = isset($_POST["id_perfil"]) ? (int)$_POST["id_perfil"] : 0;
$sueldo = isset($_POST["sueldo"]) ? (int)$_POST["sueldo"] : 0;
$foto = isset($_FILES["foto"]["name"]) ? $_FILES["foto"]["name"] : "";

$respuesta = new stdClass();

$empleado = new Empleado();
$empleado->nombre = $nombre;
$empleado->clave = $clave;
$empleado->correo = $correo;
$empleado->id_perfil = $id_perfil;
$empleado->sueldo = $sueldo;
$empleado->foto = $foto;

if($empleado->AgregarF())
{
    $respuesta->exito = true;
    $respuesta->mensaje = "SE AGREGO correctamente el Empleado";
}
else
{
    $respuesta->exito = false;
    $respuesta->mensaje = "NO SE HA PODIDO AGREGAR el empleado";
}

var_dump(json_encode($respuesta));

?>