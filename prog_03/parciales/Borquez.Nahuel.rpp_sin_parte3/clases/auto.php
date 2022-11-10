<?php

namespace Borquez\Nahuel;

use PDO;
use PDOException;
use stdClass;

class Auto
{
    protected string $patente;
    protected string $marca;
    protected string $color;
    protected float $precio;
    
    public function __construct($patente, $marca, $color, $precio)
    {
        $this->patente = $patente;
        $this->marca = $marca;
        $this->color = $color;
        $this->precio = (float)$precio;
    }
    
    /////////   GETERS  //////////
    
    public function getPatente()
    {
        return $this->patente;
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getPrecio()
    {
        return $this->precio;
    }
    ////////////////////////////////

    
    public function toJSON()
    {
        $obj = new stdClass();
        $obj->patente = $this->patente;
        $obj->marca = $this->marca;
        $obj->color = $this->color;
        $obj->precio = $this->precio;

        return json_encode($obj);
    }

    public function guardarJSON($path)
    {
        $respuesta = new stdClass();
        
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
            $respuesta->mensaje = "Se guardo Correctamente";
        }
        else
        {
            $respuesta->exito = false;
            $respuesta->mensaje =" NO se pudo Guardar Correctamente";
        }

        return json_encode($respuesta);
    }


    public static function traerJSON($path)
    {
        $autosArray = array();
        $string = file_get_contents($path);
        $objsDecode = json_decode($string);

        foreach($objsDecode as $obj)
        {
            $auto = new Auto($obj->patente, $obj->marca, $obj->color, $obj->precio);
            array_push($autosArray, $auto);
        }

        return $autosArray;
    }


    public static function verificarAutoJSON($auto)
    {
        $retorno = new stdClass();
        $flag = 0;

        //CAMBIAR PATH
        $array = Auto::traerJSON("./archivos/autos.json");
        foreach($array as $element) 
        {
            if($auto->patente == $element->patente) 
            {
                $flag = 1;
            }
        }

        if($flag > 0)
        {
            $retorno->existe = true;
            $retorno->mensaje = "El auto se encuentra REGISTRADO";
        }
        else
        {
            $retorno->existe = false;
            $retorno->mensaje = "El auto {$auto->patente}  NO se encuentra REGISTRADO!! ";
        }

        return json_encode($retorno);
    }

}


?>