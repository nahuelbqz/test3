<?php

require_once("./clases/Usuario.php");
use Borquez\Usuario;

//if(isset($_GET["tabla"]))
//{
    $usuarios = Usuario::TraerTodos();
//}

    //OTRA TABLA
    $tabla = 
    "<table>" . 
    "<thead>";

    foreach ($usuarios[0] as $key => $value) {
        if($key != "clave") {
            if($key != "id_perfil") {
                $tabla .= "<th>" . $key . "</th>";
            }
        }
    }

    $tabla .= "<th>ACCIONES</th>";
    $tabla .= "</thead>";

    //body
    $tabla .= "<tbody>";
    for ($i=0; $i < count($usuarios); $i++) { 
        $tabla .= "<tr>";//fila
        foreach ($usuarios[$i] as $key => $value) {
            if($key != "clave"){
                if($key != "id_perfil") {
                    $tabla .= "<td>" . $value . "</td>";//valor column
                }
            }
        }
        $usuario = json_encode($usuarios[$i]);
        $tabla .= "<td><input type='button' value='Modificar' onclick='ModeloParcial.Modificar({$usuario})'></td>";
        $tabla .= "<td><input type='button' value='Eliminar' onclick='ModeloParcial.EliminarUsuario({$usuario})'></td>";
        $tabla .= "</tr>";
    }
    $tabla .= "</tbody>";
    $tabla .= "</table>";

    echo $tabla;

?>

<!--

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
                <th>NOMBRE</th>
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

                -->
