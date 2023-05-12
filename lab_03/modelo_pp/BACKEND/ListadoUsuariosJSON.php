<?php

require_once("./clases/Usuario.php");
use Borquez\Usuario;

if(isset($_GET["accion"]))
{
    var_dump(json_encode(Usuario::TraerTodosJSON()));
}
else
{
    echo "Error";
}