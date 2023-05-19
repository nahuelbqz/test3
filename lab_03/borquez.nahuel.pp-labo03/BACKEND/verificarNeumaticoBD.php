<?php
require_once("./clases/accesoDatos.php");
require_once("./clases/neumaticoBD.php");

use Borquez\Nahuel\NeumaticoBD;

$obj_neumatico = isset($_POST["obj_neumatico"]) ? $_POST["obj_neumatico"] : NULL;

if($obj_neumatico) 
{
    $neumaticos = NeumaticoBD::traer();

    $obj = json_decode($obj_neumatico, true);

    $neumatico = new NeumaticoBD($obj["marca"], $obj["medidas"], 0, 0, "");
    
    if($neumatico->existe($neumaticos)) 
    {
        for ($i = 0; $i < count($neumaticos); $i++) {
            if ($neumaticos[$i]->getMarca() == $neumatico->getMarca() && $neumaticos[$i]->getMedidas() == $neumatico->getMedidas()) {
                $neumatico = $neumaticos[$i];
                break;
            }
        }

        echo $neumatico->ToJSON();
    } 
    else 
    {
        echo "{}";
    }

}

?>