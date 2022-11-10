<?php

require_once("./clases/auto.php");
require_once("./clases/IParte1.php");
require_once("./clases/IParte2.php");
require_once("./clases/IParte3.php");

use Borquez\Nahuel\Auto;

/*
use PDO;
use PDOException;
use stdClass;
*/

class AutoBD extends Auto implements IParte1,IParte2,IParte3
{
    protected $pathFoto;//string

    public function __construct($patente="", $marca="", $color="", $precio=0, $pathFoto=NULL) 
    {
        parent::__construct($patente, $marca, $color, $precio);

        $this->pathFoto = $pathFoto;
    }

    //////////   GETERS - SETERS   /////////////

    public function setPathFoto($pathFoto)
    {
        $this->pathFoto = $pathFoto;
    }
    public function getPathFoto()
    {
        return $this->pathFoto;
    }
    
    public function setMarca($marca)
    {
        $this->marca = $marca;
    }
    public function getMarca()
    {
        return $this->marca;
    }
        
    public function setPatente($patente)
    {
        $this->patente = $patente;
    }
    public function getPatente()
    {
        return $this->patente;
    }
        
    public function setColor($color)
    {
        $this->color = $color;
    }
    public function getColor()
    {
        return $this->color;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    public function getPrecio()
    {
        return $this->precio;
    }

    /////////////////   PARTE 1   /////////////////////

    public function toJSON()
    {
        $obj = new stdClass();

        $obj->patente = $this->patente;
        $obj->marca = $this->marca;
        $obj->precio = $this->precio;
        $obj->color = $this->color;

        $obj->pathFoto = $this->pathFoto;

        return json_encode($obj);
    }


    public function agregar()
    {
        if($this->pathFoto != NULL)
        {
            $objetoPDO = new PDO('mysql:host=localhost;dbname=garage_bd;charset=utf8', "root", "");
            $consulta =$objetoPDO->prepare("INSERT INTO autos (patente, marca, color, precio, foto)"
                                                    . "VALUES(:patente, :marca, :color, :precio, :foto)");
                    
            $consulta->bindValue(':patente', $this->patente, PDO::PARAM_STR);
            $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
            $consulta->bindValue(':color', $this->color, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);

            $consulta->bindValue(':foto', $this->pathFoto, PDO::PARAM_STR);

            return $consulta->execute();
        }   
        else
        {
            $objetoPDO = new PDO('mysql:host=localhost;dbname=garage_bd;charset=utf8', "root", "");
            $consulta = $objetoPDO->prepare("INSERT INTO autos (patente, marca, color, precio, foto)"
                                                    . "VALUES(:patente, :marca, :color, :precio, :foto)");

            $consulta->bindValue(':patente', $this->patente, PDO::PARAM_STR);
            $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
            $consulta->bindValue(':color', $this->color, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
            $consulta->bindValue(':foto', $this->pathFoto, PDO::PARAM_STR);

            return $consulta->execute();  
        }
    }

    static public function traer()
    {
        $elementos = array();

        $objetoPDO = new PDO('mysql:host=localhost;dbname=garage_bd;charset=utf8', "root", "");
        $sql = $objetoPDO->query("SELECT * FROM autos");   
        $resultado = $sql->fetchall();

        foreach($resultado as $fila) 
        {
            if($fila[4] === NULL)
            {
                $fila[4] = "VACIO";
            }

            $obj = new AutoBD($fila[0], $fila[1], $fila[2], (int)$fila[3], $fila[4]);
            //$obj->toJSON();
            array_push($elementos, $obj);
        }

        return $elementos;
    }



    //////////////////   PARTE 2    ////////////////////////
    
    public static function eliminar($patente)
    {
        $objetoPDO = new PDO('mysql:host=localhost;dbname=garage_bd;charset=utf8', "root", "");
        $sql = $objetoPDO->prepare("DELETE FROM autos WHERE patente = :patente");
        
        $sql->bindValue(':patente', $patente, PDO::PARAM_STR);

        return $sql->execute();
    }

    public function modificar()
    {
        $objetoPDO = new PDO('mysql:host=localhost;dbname=garage_bd;charset=utf8', "root", "");
        $sql = $objetoPDO->prepare('UPDATE autos SET marca=:marca, color=:color, precio=:precio, foto=:foto 
                                    WHERE autos.patente=:patente');
    
        $patente = $this->patente;
        $marca = $this->marca;
        $color = $this->color;
        $precio = $this->precio;
        $foto = $this->pathFoto;
    
        $sql->bindValue(':patente', $patente, PDO::PARAM_STR);
        $sql->bindValue(':marca', $marca, PDO::PARAM_STR);
        $sql->bindValue(':color', $color, PDO::PARAM_STR);
        $sql->bindValue(':precio', $precio, PDO::PARAM_INT);
        $sql->bindValue(':foto', $foto, PDO::PARAM_STR);

        return $sql->execute();
    }


    //////////////////    PARTE 3    //////////////////////

    public function existe($arrayAutosBD)
    {
        $retorno = false;

        foreach($arrayAutosBD as $item)
        {
            if($item->patente == $this->patente)
            {
                $retorno = true;
                break;
            }
        }
        
        return $retorno;
    }

    public function guardarEnArchivo()
    {
        $nuevoPath = "./archivos/autosBorrados/" . $this->patente .".". $this->marca . ".borrado." . date("His") . "." . pathinfo($this->pathFoto, PATHINFO_EXTENSION);
        copy("./autos/imagenes".$this->pathFoto, $nuevoPath);
        unlink("./autos/imagenes".$this->pathFoto);

        $archivo = fopen("./archivos/autosbd_borrados.txt", "a");
        $cadena = $this->patente . " - " . $this->marca . " - " . $this->color . " - " . $this->precio . " - " . $nuevoPath;
        $escritura = fwrite($archivo, $cadena);

        if($escritura > 0)
        {
            $jsonRetorno["exito"] = true;
            $jsonRetorno["mensaje"] = "Se ha escrito con exito el archivo.";
        }
        else
        {
            $jsonRetorno["exito"] = false;
            $jsonRetorno["mensaje"] = "Hubo un error al intentar escribir el archivo.";
        }
        
        echo $jsonRetorno["mensaje"];
        return $jsonRetorno;
    }


}


?>