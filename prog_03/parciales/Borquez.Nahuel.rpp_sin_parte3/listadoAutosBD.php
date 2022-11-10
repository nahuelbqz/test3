<?php

require_once "./clases/autoBD.php";

$autos = AutoBD::traer();

if(isset($_GET["tabla"]) && $_GET["tabla"] == "mostrar")
{
    //tabla

}
else
{
    foreach($autos as $obj)
    {
        echo $obj->toJSON() . "; \r\n";
    }
    die();
    //var_dump(json_encode($autos));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
            table, th, td{
                border: 1px solid black;
                border-collapse: collapse;
            }
    </style>
    <title>LISTADO DE AUTOS</title>
</head>
<body>
<table class ="table table -ligth">
        <thead class="thead-light">
             <tr>
                <th>PATENTE</th>
                <th>MARCA</th>
                <th>COLOR</th>
                <th>PRECIO</th>
                <th>FOTO</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($autos as $obj){
            ?>
            <tr>
                <td><?php echo $obj->getPatente();?></td>
                <td><?php echo $obj->getMarca();?></td>
                <td><?php echo $obj->getColor();?></td>
                <td><?php echo $obj->getPrecio();?></td>
                <td><img src="<?php echo $obj->getPathFoto();?>" width="50px" height="50px"></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
</body>
</html>