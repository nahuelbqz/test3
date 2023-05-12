<?php

require_once("./clases/Usuario.php");
use Borquez\Usuario;

if(isset($_GET["tabla"]))
{
    $usuarios = Usuario::TraerTodos();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LISTADO DE USUARIOS</title>
</head>
<body>
<table class ="table table -ligth">
        <thead class="thead-light">
             <tr>
                <th>ID</th>
                <th> NOMBRE</th>
                <th>CORREO</th>
                <th>ID PERFIL</th>
                <th>PERFIL</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($usuarios as $obj){
            ?>
            <tr>
                <td> <?php echo $obj->id;?> </td>
                <td> <?php echo $obj->nombre;?> </td>
                <td> <?php echo $obj->correo;?> </td>
                <td> <?php echo $obj->id_perfil;?> </td>
                <td> <?php echo $obj->perfil;?> </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
</body>
</html>