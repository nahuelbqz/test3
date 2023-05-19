<?php
require_once("./clases/accesoDatos.php");
require_once("./clases/neumaticoBD.php");

use Borquez\Nahuel\NeumaticoBD;


$marca = isset($_POST["marca"]) ? $_POST["marca"] : NULL;
$medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : NULL;
$precio = isset($_POST["precio"]) ? $_POST["precio"] : 0;
$pathFoto = isset($_FILES["foto"]) ? $_FILES["foto"] : NULL;

$retorno = new stdClass();
$retorno->exito = false;
$retorno->mensaje = "No se pudo agregar el neumatico";

if($marca && $medidas && $precio && $pathFoto) 
{
    $path =  NeumaticoBD::getPath($pathFoto, $marca);
    $neumatico = new NeumaticoBD($marca, $medidas, $precio, 0, $path);
	
    $neumaticos = NeumaticoBD::traer();

    if(! $neumatico->existe($neumaticos)) {
        if($neumatico->agregar()) {
            $retorno->exito = true;
            $retorno->mensaje = "Neumatico agregado";
            NeumaticoBD::guardarImagen($path);
        }
    } 
	else {
        $retorno->mensaje = "No se pudo agregar el neumatico, ya existe!";
    }
}

echo json_encode($retorno);

?>