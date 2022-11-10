<?php

require_once "./clases/auto.php";

use Borquez\Nahuel\Auto;

$autos = Auto::traerJSON('./archivos/autos.json');
//var_dump($autos);

foreach($autos as $obj)
{
    echo $obj->getPatente() . " - " . $obj->getMarca() . " - " . $obj->getColor() ." - " . $obj->getPrecio() . "<br>";       
}

?>