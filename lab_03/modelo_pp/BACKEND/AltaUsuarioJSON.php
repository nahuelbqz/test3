<?php 

require_once("./clases/usuario.php");
use Borquez\Usuario;

$correo = isset($_POST["correo"]) ?  $_POST["correo"] : NULL;
$clave = isset($_POST["clave"]) ? $_POST["clave"] : NULL;
//post
$nombre = isset($_REQUEST["nombre"]) ? $_REQUEST["nombre"] : NULL;

$usuario = new Usuario();
$usuario->nombre = $nombre;
$usuario->clave = $clave;
$usuario->correo = $correo;

var_dump($usuario->GuardarEnArchivo());


?>