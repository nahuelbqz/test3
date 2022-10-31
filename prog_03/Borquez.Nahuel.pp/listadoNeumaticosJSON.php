<?php

use Borquez\Nahuel\Neumatico;

require_once("./clases/neumatico.php");

if(isset($_GET["accion"]))
{
    $neumaticos = Neumatico::traerJSON('./archivos/neumaticos.json');
    //var_dump($neumaticos);

    foreach($neumaticos as $obj)
    {
        echo $obj->getMarca() . " - " . $obj->getMedidas() . " - " . $obj->getPrecio() . "<br>";       
    }
}
else
{
    echo "Error";
}