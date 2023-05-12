<?php

require_once("./clases/Empleado.php");

use Borquez\Empleado;

if(isset($_GET["tabla"]))
{
    $empleados = Empleado::TraerTodosF();
}
//var_dump($empleados = Empleado::TraerTodosF());

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LISTADO DE EMPLEADOS</title>
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
                <th>SUELDO</th>
                <th>FOTO</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($empleados as $obj){
            ?>
            <tr>
                <td><?php echo $obj->id;?></td>
                <td><?php echo $obj->nombre;?></td>
                <td><?php echo $obj->correo;?></td>
                <td><?php echo $obj->id_perfil;?></td>
                <td><?php echo $obj->perfil;?></td>
                <td><?php echo $obj->sueldo;?></td>
                <td><img src="<?php echo $obj->foto;?>" width="50px" height="50px"></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
</body>
</html>