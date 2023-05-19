<?php
namespace Borquez\Nahuel;
use stdClass;

class Neumatico
{
    protected string $marca;
    protected string $medidas;
    protected float $precio; 

    public function __construct(string $marca = "", string $medidas = "", float $precio = 0) 
    {
        $this->marca = $marca;
        $this->medidas = $medidas;
        $this->precio = $precio;
    }

    public function toJSON(): string
    {
        $obj_JSON = new stdClass();
        $obj_JSON->marca = $this->marca;
        $obj_JSON->medidas = $this->medidas;
        $obj_JSON->precio = $this->precio;

        return json_encode($obj_JSON);
    }

    public function guardarJSON(string $path)
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "Error al guardar el neumatico";

        //ABRO EL ARCHIVO
        $ar = fopen($path, "a"); //A - append

        //ESCRIBO EN EL ARCHIVO CON FORMATO: $this->ToJSON()
        $cant = fwrite($ar, "{$this->ToJSON()},\r\n");

        if ($cant > 0) {
            $retorno->exito = true;
            $retorno->mensaje = "Neumatico Guardado satisfactoriamente!";
        }
        
        //CIERRO EL ARCHIVO
        fclose($ar);

        return json_encode($retorno);
       
    }

    public static function traerJSON(string $path): array
    {
        $array_neumaticos = array();

        //ABRO EL ARCHIVO
        $ar = fopen($path, "r");
        $contenido = "";

        //LEO LINEA X LINEA DEL ARCHIVO 
        while (!feof($ar)) {
            $contenido .= fgets($ar);
        }
        
        //CIERRO EL ARCHIVO
        fclose($ar);

        $array_contenido = explode(",\r\n", $contenido);

        for ($i = 0; $i < count($array_contenido); $i++) 
        {
            if ($array_contenido[$i] != "") {
                $neumatico =  json_decode($array_contenido[$i], true);
                $marca = $neumatico["marca"];
                $medidas = $neumatico["medidas"];
                $precio = $neumatico["precio"];
                $neumatico = new Neumatico($marca, $medidas, $precio);


                array_push($array_neumaticos, $neumatico);
                //var_dump($array_neumaticos);
            }
        }

        return $array_neumaticos;
    }

    public static function verificarNeumaticoJSON(Neumatico $neumatico)
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "";

        $acumPrecio = 0;

        $neumaticos = Neumatico::traerJSON("./archivos/neumaticos.json");

        foreach ($neumaticos as $neuma) {

            if ($neuma->marca == $neumatico->marca && $neuma->medidas == $neumatico->medidas) 
            {
                $retorno->exito = true;
                $acumPrecio += $neuma->precio;
            }
        }

        $retorno->mensaje = "La sumatoria del precio de los neumaticos fue: " . $acumPrecio;

        return json_encode($retorno);
    }


}
