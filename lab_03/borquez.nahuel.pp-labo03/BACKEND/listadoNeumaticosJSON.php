<?php

require_once("./clases/neumatico.php");

use Borquez\Nahuel\Neumatico;

$neumaticos = Neumatico::traerJSON("./archivos/neumaticos.json");

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

?>