<?php

require_once("./clases/neumatico.php");
require_once("./clases/IParte1.php");
require_once("./clases/IParte2.php");
require_once("./clases/IParte3.php");
require_once("./clases/IParte4.php");

use Borquez\Nahuel\Neumatico;
/*
use PDO;
use PDOException;
use stdClass;
*/

class NeumaticoBD extends Neumatico implements IParte1,IParte2,IParte3,IParte4
{
    protected $id;//int
    protected $pathFoto;//string

    public function __construct($marca=NULL, $medidas=NULL, $precio=NULL, $id=NULL, $pathFoto=NULL) 
    {
        parent::__construct($marca, $medidas, $precio);

        $this->id = $id;
        $this->pathFoto = $pathFoto;
    }

    //////////   GETERS Y SETERS   //////////////////////////
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }

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
        
    public function setMedidas($medidas)
    {
        $this->medidas = $medidas;
    }
    public function getMedidas()
    {
        return $this->medidas;
    }
        
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }
    public function getPrecio()
    {
        return $this->precio;
    }

    //////////////////////////////////////

    public function toJSON()
    {
        $obj = new stdClass();

        $obj->marca = $this->marca;
        $obj->medidas = $this->medidas;
        $obj->precio = $this->precio;

        $obj->id = $this->id;
        $obj->pathFoto = $this->pathFoto;

        return json_encode($obj);
    }


    public function agregar()
    {
        if($this->pathFoto != NULL)
        {
            $destino = isset($_FILES["foto"]) ? "./neumaticos/imagenes" . $this->pathFoto : NULL;
            $extension = pathinfo($destino, PATHINFO_EXTENSION);
            $hora = date("his");
            $nuevoDestino = "./neumaticos/imagenes/{$this->marca}.{$hora}.{$extension}";
            if(move_uploaded_file($_FILES["foto"]["tmp_name"], $nuevoDestino))
            {
                $objetoPDO = new PDO('mysql:host=localhost;dbname=gomeria_bd;charset=utf8', "root", "");
                $consulta =$objetoPDO->prepare("INSERT INTO neumaticos (marca, medidas, precio, foto)"
                                                            . "VALUES(:marca, :medidas, :precio, :foto)");
                    
                $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
                $consulta->bindValue(':medidas', $this->medidas, PDO::PARAM_STR);
                $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
                $arrDestino = explode("/", $nuevoDestino);
                $arrDestino[3] = "./" . $arrDestino[3];
                $consulta->bindValue(':foto', $arrDestino[3], PDO::PARAM_STR);

                return $consulta->execute();
            }
        }   
        else
        {
            $objetoPDO = new PDO('mysql:host=localhost;dbname=gomeria_bd;charset=utf8', "root", "");
            $consulta = $objetoPDO->prepare("INSERT INTO neumaticos (marca, medidas, precio)"
                                                    . "VALUES(:marca, :medidas, :precio)");

            $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
            $consulta->bindValue(':medidas', $this->medidas, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);

            return $consulta->execute();  
        }
    }

    static public function traer()
    {
        $elementos = array();

        $objetoPDO = new PDO('mysql:host=localhost;dbname=gomeria_bd;charset=utf8', "root", "");
        $sql = $objetoPDO->query("SELECT * FROM neumaticos");   
        $resultado = $sql->fetchall();

        foreach($resultado as $fila) 
        {
            if($fila[4] === NULL)
            {
                $fila[4] = "VACIO";
            }
            $obj = new NeumaticoBD($fila[1], $fila[2], (int)$fila[3], $fila[0], $fila[4]);
            //$obj->toJSON();
            array_push($elementos, $obj);
        }

        return $elementos;
    }


    /////////   PARTE 2    //////////////
    
    public static function eliminar($id)
    {
        $objetoPDO = new PDO('mysql:host=localhost;dbname=gomeria_bd;charset=utf8', "root", "");
        $sql = $objetoPDO->prepare("DELETE FROM neumaticos WHERE id = :id");
        
        $sql->bindValue(':id', $id, PDO::PARAM_INT);

        return $sql->execute();
    }

    public function modificar()
    {
        $objetoPDO = new PDO('mysql:host=localhost;dbname=gomeria_bd;charset=utf8', "root", "");
        $sql = $objetoPDO->prepare('UPDATE neumaticos SET marca=:marca, medidas=:medidas, precio=:precio, foto=:foto 
                                    WHERE neumaticos.id=:id');
    
        $id = $this->id;
        $marca = $this->marca;
        $medidas = $this->medidas;
        $precio = $this->precio;
        $foto = $this->pathFoto;
    
        $sql->bindValue(':id', $id, PDO::PARAM_INT);
        $sql->bindValue(':marca', $marca, PDO::PARAM_STR);
        $sql->bindValue(':medidas', $medidas, PDO::PARAM_STR);
        $sql->bindValue(':precio', $precio, PDO::PARAM_INT);
        $sql->bindValue(':foto', $foto, PDO::PARAM_STR);

        return $sql->execute();
    }

    ////////////    PARTE 3     ///////////////////

    public function existe($arrayObj)
    {
        $retorno = false;

        foreach($arrayObj as $item)
        {
            if($item->marca == $this->marca && $item->medidas == $this->medidas)
            {
                $retorno = true;
                break;
            }
        }
        
        return $retorno;
    }


    /////////////       PARTE 4      ////////////////////////////

    //funciona hasta ahi

    public function guardarEnArchivo()
    {
        $nuevoPath = "./archivos/neumaticosBorrados/" . $this->id .".". $this->marca . ".borrado." . date("His") . "." . pathinfo($this->pathFoto, PATHINFO_EXTENSION);
        copy("./neumaticos/imagenes".$this->pathFoto, $nuevoPath);
        unlink("./neumaticos/imagenes".$this->pathFoto);

        $archivo = fopen("./archivos/neumaticosbd_borrados.txt", "a");
        $cadena = $this->marca . " - " . $this->medidas . " - " . $this->precio . " - " . $nuevoPath;
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