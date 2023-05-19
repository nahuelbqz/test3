<?php

require_once("./clases/Usuario.php");
use Borquez\Usuario;
/*
if(isset($_GET["accion"]))
{
    var_dump(json_encode(Usuario::TraerTodosJSON()));
}
else
{
    echo "Error";
}
*/

$array_usuarios = Usuario::TraerTodosJSON();

// for ($i=0; $i < count($array_usuarios); $i++) { 
//     $usuario = $array_usuarios[$i];
//         echo $usuario->ToJSON() . "\n";    
// }

echo json_encode($array_usuarios);
