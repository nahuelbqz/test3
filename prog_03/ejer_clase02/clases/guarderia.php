<?php

namespace Negocios; 

require_once "mascota.php";
use Animalitos\Mascota;

class Guarderia
{

    public $nombre; 
    public $mascotas;

    public function __construct(string $nombre)
    {
        $this->nombre = $nombre;
        $this->mascotas = array();
    }

    public static function equals(Guarderia $guarderia, Mascota $mascota) : bool
    {
        foreach($guarderia->mascotas as $item) 
        {
            if($item->equals($mascota))
            {
                return true;
            }
        }

        return false;
    }

    public function add(Mascota $m) : bool
    {
        $retorno = false;

        if(Guarderia::equals($this, $m) != true)
        {
            //$mascotas[] = $m;
            array_push($this->mascotas, $m);
            $retorno = true;
        }

        return $retorno;
    }

    public function toString() : string
    {
        $retorno = " {$this->nombre}: <br>";

        foreach($this->mascotas as $m)
        {
            $retorno = $retorno . $m->toString();
            $retorno = $retorno . "<br>";
        }


        $total = 0;
        $promedio = 0;
        $cant = count($this->mascotas,COUNT_NORMAL);

        for($i = 0; $i < count($this->mascotas); $i++)
        {
            $total = $this->mascotas[$i]->edad + $total; 
            $cant = $i + $cant;
        }
        $promedio = $total / $cant;

        $retorno = $retorno . "{$cant}Promedio de edades: {$promedio}";

        return $retorno;
    }

}
    

?>