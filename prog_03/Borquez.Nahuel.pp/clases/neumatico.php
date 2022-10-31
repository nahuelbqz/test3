<?php

namespace Borquez\Nahuel;

use PDO;
use PDOException;
use stdClass;

class Neumatico
{
    protected string $marca;
    protected string $medidas;
    protected float $precio;
    
    public function __construct($marca, $medidas, $precio)
    {
        $this->marca = $marca;
        $this->medidas = $medidas;
        $this->precio = (float)$precio;
    }
    
    /////////   GETERS  //////////
    public function getMarca()
    {
        return $this->marca;
    }

    public function getMedidas()
    {
        return $this->medidas;
    }

    public function getPrecio()
    {
        return $this->precio;
    }
    ////////////////////////////////

    
    public function toJSON()
    {
        $obj = new stdClass();
        $obj->marca = $this->marca;
        $obj->medidas = $this->medidas;
        $obj->precio = $this->precio;

        return json_encode($obj);
    }

    public function guardarJSON($path)
    {
        $respuesta = new stdClass();
        //$path = "./archivos/neumaticos.json";
        
        if(!(file_exists($path)))
        {
            $ar = fopen($path, "a");
            $cant = fwrite($ar, "[".$this->ToJSON()."]");
            fclose($ar);
        }
        else
        {
            $ar = fopen($path,"r");
            $aux = fread($ar,filesize($path));
            $lectura = explode("]", $aux);
            fclose($ar);

            $ar = fopen($path,"w");
            $cant = fwrite($ar, $lectura[0].",\r\n".$this->ToJSON()."]");
            fclose($ar);
        }
        
        if($cant>0)
        {
            $respuesta->exito = true;
            $respuesta->mensaje = "Se a Guardado Correctamente";
        }
        else
        {
            $respuesta->exito = false;
            $respuesta->mensaje =" NO se a Guardado Correctamente";
        }

        return json_encode($respuesta);
    }


    public static function traerJSON($path)
    {
        $neumaticosArray = array();
        $string = file_get_contents($path);
        $decodeo = json_decode($string);

        foreach($decodeo as $element)
        {
            $neumatico = new Neumatico($element->marca,$element->medidas,$element->precio);
            array_push($neumaticosArray, $neumatico);
        }

        return $neumaticosArray;
    }


    public static function verificarNeumaticoJSON($neumatico)
    {
        $sumatoriaPrecios = 0;
        $retorno = new stdClass();

        $array = Neumatico::traerJSON("./archivos/neumaticos.json");
        foreach($array as $element) 
        {
            if($neumatico->marca == $element->marca && $neumatico->medidas == $element->medidas) 
            {
                $sumatoriaPrecios += $element->precio;
            }
        }

        if($sumatoriaPrecios > 0)
        {
            $retorno->existe = true;
            $retorno->mensaje = "Sumatoria de precios neumaticos misma marca y misma medida: " . $sumatoriaPrecios;
        }
        else
        {
            $retorno->existe = false;
            $retorno->mensaje = "El neumatico de marca {$neumatico->marca}, medidas {$neumatico->medidas} NO existe";
        }
        return json_encode($retorno);
    }

}


?>