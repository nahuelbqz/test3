<?php
require_once("./clases/accesoDatos.php");
require_once("./clases/neumaticoBD.php");

use Borquez\Nahuel\NeumaticoBD;

$neumatico_json = isset($_POST["neumatico_json"]) ? $_POST["neumatico_json"] : NULL;

$retorno = new stdClass();
$retorno->exito = false;
$retorno->mensaje = "No se pudo eliminar el neumatico";


if ($neumatico_json) 
{
    $obj = json_decode($neumatico_json, true);

    $neumatico = new NeumaticoBD(
        $obj["marca"],
        $obj["medidas"],
        $obj["precio"],
        $obj["id"],
        $obj["pathFoto"],
    );

    if (NeumaticoBD::eliminar($neumatico->getId())) 
    {
        $neumatico->guardarEnArchivo();
        $retorno->exito = true;
        $retorno->mensaje = "Neumatico eliminado";
    }

    echo json_encode($retorno);

} 
else if (count($_GET) == 0) 
{
    $neumaticosBorrados = NeumaticoBD::MostrarBorrados();

    $tablaHTML = '<html>
    <head><title>Listado de Neumaticos BORRADOS</title></head>
    <body>
    
    <h1>Listado de Neumaticos BORRADOS</h1>
    
    <table>
    <tr>
      <th style="padding:0 15px 0 15px;"><strong>ID</strong></th>      
      <th style="padding:0 15px 0 15px;"><strong>MARCA </strong></th>
      <th style="padding:0 15px 0 15px;"><strong>MEDIDAS </strong></th>
      <th style="padding:0 15px 0 15px;"><strong>PRECIO </strong></th>
      <th style="padding:0 15px 0 15px;"><strong>FOTO </strong></th>
    </tr>
    
    ';

    foreach ($neumaticosBorrados as $neumatico) 
    {
        $stringProducto = '<tr>
        <td style="padding:0 15px 0 15px;"><strong>'. $neumatico->getId().'</strong></td>
        <td style="padding:0 15px 0 15px;"><strong>'. $neumatico->getMarca() .'</strong></td>
        <td style="padding:0 15px 0 15px;"><strong>'. $neumatico->getMedidas() .'</strong></td>
        <td style="padding:0 15px 0 15px;"><strong>'. $neumatico->getPrecio() .'</strong></td>
        <td style="padding:0 15px 0 15px;"><img src="'. $neumatico->getFoto() .'" width="100" height="100"></td>
        </tr>
        
        ';

        $tablaHTML .= $stringProducto;       
    }

    $tablaHTML .= "</table>
    </body>
    </html>";
    
    echo $tablaHTML;

}
