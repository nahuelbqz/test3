<?php

require_once "./clases/autoBD.php";

$patente = isset($_POST["patente"]) ? $_POST["patente"] : "";
$marca = isset($_POST["marca"]) ? $_POST["marca"] : "";
$color = isset($_POST["color"]) ? $_POST["color"] : "";
$precio = isset($_POST["precio"]) ? $_POST["precio"] : "";
$foto = isset($_POST["foto"]) ? $_POST["foto"] : "";
$foto = $_FILES;

if(isset($foto["foto"]["name"])) 
{
    $fotoName = $foto["foto"]["name"];
    $fotoNameTemp = $foto["foto"]["tmp_name"];
    $tipoArchivo = pathinfo($fotoName, PATHINFO_EXTENSION);
    $fotoPath = "./autos/imagenes/" . $patente . "." . date("His") . "." . $tipoArchivo;

    //var_dump($fotoPath); die();
}

$autoBD = new AutoBD($patente, $marca ,$color, $precio, $fotoPath);

$rta = new stdClass();
if($autoBD->existe(AutoBD::traer()))
{
    echo "El auto YA existe en la BD";
    return "El auto YA existe en la BD";
}
else
{
    if($autoBD->agregar()) 
    {  
        move_uploaded_file($fotoNameTemp, $fotoPath);//retorna false no se pq
        $rta->exito = true;
        $rta->mensaje = "Agregado con exito";
    }
    else
    {
        $rta->exito = false;
        $rta->mensaje = "NO se pudo agregar el neumatico!!";
    }
}

echo json_encode($rta);
return json_encode($rta);

?>