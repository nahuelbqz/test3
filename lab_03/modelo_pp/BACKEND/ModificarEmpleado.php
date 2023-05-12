<?php

require_once("./clases/Empleado.php");
use Borquez\Empleado;

$empleado_json = isset($_POST["empleado_json"]) ?  $_POST["empleado_json"] : NULL;
$foto = isset($_FILES["foto"]["name"]) ? $_FILES["foto"]["name"] : "";

$params = json_decode($empleado_json);
//var_dump($params);

$obj = new stdClass();

$empleado = new Empleado();
$empleado->id = $params->id;
$empleado->nombre = $params->nombre;
$empleado->clave = $params->clave;
$empleado->correo = $params->correo;
$empleado->id_perfil = $params->id_perfil;
$empleado->sueldo = $params->sueldo;

if($foto == "")
{
    $empleado->foto = $params->path_foto;
}
else
{
    $empleado->foto = $foto;
}
//var_dump($empleado);die();

if($empleado->ModificarF())
{
    $obj->exito = true;
    $obj->mensaje = "Empleado modificado correctamente.";
    var_dump(json_encode($obj));
}
else
{
    $obj->exito = false;
    $obj->mensaje = "ERROR en la MODIFICACION del empleado!!";
    var_dump(json_encode($obj));
}

?>