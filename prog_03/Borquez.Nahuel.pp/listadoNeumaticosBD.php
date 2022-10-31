<?php

require_once "./clases/neumaticoBD.php";

$neumaticos = NeumaticoBD::traer();

if(isset($_GET["tabla"]) && $_GET["tabla"] == "mostrar")
{
    //tabla

}
else
{

    foreach($neumaticos as $obj)
    {
        //var_dump($obj->toJSON());
        echo $obj->toJSON();
    }
    die();
    //var_dump(json_encode($neumaticos));
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
    <title>LISTADO DE NEUMATICOS</title>
</head>
<body>
<table class ="table table -ligth">
        <thead class="thead-light">
             <tr>
                <th>ID</th>
                <th>MARCA</th>
                <th>MEDIDAS</th>
                <th>PRECIO</th>
                <th>FOTO</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($neumaticos as $obj){
            ?>
            <tr>
                <td><?php echo $obj->getId();?></td>
                <td><?php echo $obj->getMarca();?></td>
                <td><?php echo $obj->getMedidas();?></td>
                <td><?php echo $obj->getPrecio();?></td>
                <td><img src="<?php echo $obj->getPathFoto();?>" width="50px" height="50px"></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
</body>
</html>

