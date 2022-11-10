<?php

require_once "./clases/neumaticoBD.php";

$marca = isset($_POST["marca"]) ? $_POST["marca"] : "";
$medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : "";
$precio = isset($_POST["precio"]) ? $_POST["precio"] : "";
$foto = $_FILES;

if(isset($foto["foto"]["name"])) 
{
    $fotoName = $foto["foto"]["name"];
    $fotoNameTemp = $foto["foto"]["tmp_name"];
    $tipoArchivo = pathinfo($fotoName, PATHINFO_EXTENSION);
    $fotoPath = "./neumaticos/imagenes/" . $marca . "." . date("His") . "." . $tipoArchivo;

    //var_dump($fotoPath); die();
}

$neumaticoBD = new NeumaticoBD($marca, $medidas, $precio, 0, $fotoPath);

$rta = new stdClass();
if($neumaticoBD->existe(NeumaticoBD::traer()))
{
    echo "El neumatico YA existe.";
    return "El neumatico YA existe.";
}
else
{
    if($neumaticoBD->agregar()) 
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