<?php
require_once("./clases/neumatico.php");
use Borquez\Nahuel\Neumatico;


$marca = isset($_POST["marca"]) ? $_POST["marca"] : NULL;
$medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : NULL;
$precio = isset($_POST["precio"]) ? (float)$_POST["precio"] : 0;

if($marca && $medidas && $precio) 
{
    $neumatico = new Neumatico($marca, $medidas, $precio);
    if($neumatico) 
    {
        echo $neumatico->guardarJSON("./archivos/neumaticos.json");
    }
}


?>