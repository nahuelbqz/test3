<?php
require_once("./clases/accesoDatos.php");
require_once("./clases/neumaticoBD.php");

use Borquez\Nahuel\NeumaticoBD;

$tabla_get = isset($_GET["tabla"]) ? $_GET["tabla"] : NULL;

$neumaticos = NeumaticoBD::traer();

if($tabla_get === "mostrar")
{  
    $tablaHTML = '<html>
    <head><title>Listado de Neumaticos</title></head>
    <body>
    
    <h1>Listado de Neumaticos</h1>
    
    <table>
    <tr>
      <th style="padding:0 15px 0 15px;"><strong>ID</strong></th>      
      <th style="padding:0 15px 0 15px;"><strong>MARCA </strong></th>
      <th style="padding:0 15px 0 15px;"><strong>MEDIDAS </strong></th>
      <th style="padding:0 15px 0 15px;"><strong>PRECIO </strong></th>
      <th style="padding:0 15px 0 15px;"><strong>FOTO </strong></th>
    </tr>
    
    ';

    foreach ($neumaticos as $neumatico) 
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
else {

    echo "[";

    for ($i = 0; $i < count($neumaticos); $i++) { 
            $neumatico = $neumaticos[$i];

            if ($i == count($neumaticos) - 1) {
                echo $neumatico->ToJSON() . "\n"; 
            }
            else
            {
                echo $neumatico->ToJSON() . ",\n";  
            }  
    }

    echo "]";

}
