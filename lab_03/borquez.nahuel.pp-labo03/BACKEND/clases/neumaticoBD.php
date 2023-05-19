<?php
namespace Borquez\Nahuel;

require_once("./clases/IParte1.php");
require_once("./clases/IParte2.php");
require_once("./clases/IParte3.php");
require_once("./clases/IParte4.php");
require_once("./clases/neumatico.php");

use PDO;
use stdClass;
use PDOException;

class NeumaticoBD extends Neumatico implements IParte1, IParte2, IParte3, IParte4
{
    protected int $id;
    protected string $pathFoto;

    public function __construct(string $marca = "", string $medidas = "", float $precio = 0, int $id = 0, string $pathFoto = "") 
    {
        parent::__construct($marca, $medidas, $precio);
        $this->id = $id;
        $this->pathFoto = $pathFoto;
    }

    public function toJSON(): string
    {
        $obj_json = new stdClass();

        $obj_json->marca = $this->marca;
        $obj_json->medidas = $this->medidas;
        $obj_json->precio = $this->precio;
        $obj_json->id = $this->id;
        $obj_json->pathFoto = $this->pathFoto;

        return json_encode($obj_json);
    }

    public function getId()
    {
        return $this->id;
    }

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

    public function getFoto()
    {
        return $this->pathFoto;
    }

    public function agregar(): bool
    {
        try
        {
            $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();

            $consulta = $accesoDatos->retornarConsulta(
                "INSERT INTO neumaticos (id, marca, medidas, precio, foto)
                 VALUES(:id, :marca, :medidas, :precio, :foto)"
            );
    
            $consulta->bindValue(":id", $this->getId(), PDO::PARAM_INT);
            $consulta->bindValue(":marca", $this->getMarca(), PDO::PARAM_STR);
            $consulta->bindValue(":medidas", $this->getMedidas(), PDO::PARAM_STR);
            $consulta->bindValue(":precio", $this->getPrecio(), PDO::PARAM_INT);
            $consulta->bindValue(":foto", $this->getFoto(), PDO::PARAM_STR);
    
            $retorno = $consulta->execute();
        }
        catch (PDOException $e) 
        {
           $retorno = false;
           echo "Error!!!\n" . $e->getMessage();
        }

        return $retorno;
    }

    public static function traer(): array
    {
        try
        {
            $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();

            $consulta = $accesoDatos->retornarConsulta("SELECT * FROM neumaticos");

            $consulta->execute();

            $array_neumaticos = array();

            while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $marca = $fila["marca"];
                $medidas = $fila["medidas"];
                $precio = $fila["precio"];
                $id = $fila["id"];
                $pathFoto = $fila["foto"];
                if (!$pathFoto) {
                    $pathFoto = "";
                }

                $neumaticoBD = new NeumaticoBD($marca, $medidas, $precio, $id, $pathFoto);

                array_push($array_neumaticos, $neumaticoBD);
            }
        }
        catch (PDOException $e) 
        {
           echo "Error!!!\n" . $e->getMessage();
        }

        return $array_neumaticos;
    }

     // IMPLEMENTACION DE INTERFACE IParte2
     public static function eliminar(int $id): bool
     {
        $retorno = false;
 
        try
        {
             $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
 
             $consulta = $accesoDatos->retornarConsulta("DELETE FROM neumaticos WHERE id = :id");
 
             $consulta->bindValue(":id", $id, PDO::PARAM_INT);
 
             $consulta->execute();
 
             $total_borrado = $consulta->rowCount();

             if ($total_borrado != 0) {
                 $retorno = true;
             }
        }
         catch (PDOException $e) 
        {
             echo "Error!!!\n" . $e->getMessage();
        }
 
         return $retorno;
     }
 
     public function modificar(): bool
     {
         $retorno = false;
 
         try
         {
            $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
 
            $cadena =
                 "UPDATE neumaticos SET marca = :marca, medidas = :medidas, precio = :precio, foto = :foto WHERE id = :id";
 
            $consulta = $accesoDatos->retornarConsulta($cadena);
 
            $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
            $consulta->bindValue(":marca", $this->marca, PDO::PARAM_STR);
            $consulta->bindValue(":medidas", $this->medidas, PDO::PARAM_STR);
            $consulta->bindValue(":precio", $this->precio, PDO::PARAM_INT);
            $consulta->bindValue(":foto", $this->pathFoto, PDO::PARAM_STR);
            $consulta->execute();
 
            $total_modificado = $consulta->rowCount();
            if ($total_modificado == 1) {
                 $retorno = true;
            }
         }
         catch (PDOException $e) 
         {
             echo "Error!!!\n" . $e->getMessage();
         }
 
         return $retorno;
     }

     public function existe(array $neumaticos) : bool
     {
        $retorno = false;

        for ($i = 0; $i < count($neumaticos); $i++) {
            if ($neumaticos[$i]->marca == $this->marca && $neumaticos[$i]->medidas == $this->medidas) {
                $retorno = true;
                break;
            }
        }

        return $retorno;
     }

    // IMPLEMENTACION DE INTERFACE IParte4
    public function guardarEnArchivo()
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "No se pudo guardar en el archivo";

        if (isset($this->pathFoto)) 
        {
            $extension = pathinfo($this->pathFoto, PATHINFO_EXTENSION);
            $nombrePath = $this->id . "." . $this->marca . "." . "borrado" . "." . date("his") . "." . $extension;
            $pathCompleto = "./neumaticosBorrados/" . $nombrePath;

            rename($this->pathFoto, $pathCompleto);

            $this->pathFoto = $pathCompleto;

            //ABRO EL ARCHIVO
            $ar = fopen("./archivos/neumaticosbd_borrados.txt", "a"); //A - append

            //ESCRIBO EN EL ARCHIVO
            $cant = fwrite($ar, $this->ToJSON() . ",\r\n");

            if($cant > 0) {
                $retorno->exito = true;
                $retorno->mensaje = "Se guardo en el archivo satisfactoriamente";
            }

            //CIERRO EL ARCHIVO
            fclose($ar);
        }
    }

    public static function MostrarBorrados(): array
    {
        $path = "./archivos/neumaticosbd_borrados.txt";
        $array_neumaticos = array();
        if (file_exists($path)) {
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
            for ($i = 0; $i < count($array_contenido); $i++) {
                if ($array_contenido[$i] != "") {
                    $obj = json_decode($array_contenido[$i], true);
                    $id = $obj["id"];
                    $marca = $obj["marca"];
                    $medidas = $obj["medidas"];
                    $precio = $obj["precio"];
                    $pathFoto = $obj["pathFoto"];
                    $neumatico = new NeumaticoBD($marca, $medidas, $precio, $id, $pathFoto);
                    array_push($array_neumaticos, $neumatico);
                }
            }
        }

        return $array_neumaticos;
    }

    public static function mostrarBorradosJSON(): array
    {
        $array_neumaticos = array();

        //ABRO EL ARCHIVO
        $ar = fopen("./archivos/neumaticos_eliminados.json", "r");
        $contenido = "";
        //LEO LINEA X LINEA DEL ARCHIVO 
        while (!feof($ar)) {
            $contenido .= fgets($ar);
        }
        
        //CIERRO EL ARCHIVO
        fclose($ar);

        $array_contenido = explode(",\r\n", $contenido);
        //var_dump($array_contenido);
        for ($i = 0; $i < count($array_contenido); $i++) {
            if ($array_contenido[$i] != "" && $array_contenido[$i] != null) {
                $neumatico =  json_decode($array_contenido[$i], true);
                $marca = $neumatico["marca"];
                $medidas = $neumatico["medidas"];
                //var_dump($neumatico);
                $neumatico = new Neumatico($marca, $medidas);

                array_push($array_neumaticos, $neumatico);
            }
        }

        return $array_neumaticos;
    }


    public static function TraerProducto(int $id)
    {
        try
        {
            $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
        
            $consulta = $accesoDatos->retornarConsulta("SELECT * FROM neumaticos WHERE id = :id");
            $consulta->bindValue(":id", $id, PDO::PARAM_INT);
            $consulta->execute();

            $neumatico = null;

            while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $id = $fila["id"];
                $marca = $fila["marca"];
                $medidas = $fila["medidas"];
                $pathFoto = $fila["foto"];
                $precio = $fila["precio"];
                
                $neumatico = new NeumaticoBD(
                    $marca,
                    $medidas,
                    $precio,
                    $id,
                    $pathFoto
                );
            }
        }
        catch (PDOException $e) 
        {
            echo "Error!!!\n" . $e->getMessage();
        }

        return $neumatico;
    }

    static function mostrarModificados()
    {
        $dir = opendir("./neumaticosModificados");
        // Leo todos los ficheros de la carpeta
        echo "<table>";
        while ($elemento = readdir($dir)) {
            // Tratamos los elementos . y .. que tienen todas las carpetas
            if ($elemento != "." && $elemento != "..") {
                // Muestro el fichero
                echo
                "<tr>
                    <td>
                        <img width='100px' src='./BACKEND/neumaticosModificados/" . $elemento . "'>
                    </td>
                </tr>";
            }
        }
        echo "</table>";
    }


    static function getPath(array $foto, string $marca): string
    {
        if ($foto != NULL) 
        {
            //INDICO CUAL SERA EL DESTINO DE LA FOTO SUBIDA
            $foto_nombre = $_FILES["foto"]["name"];
            $tipoArchivo = pathinfo($foto_nombre, PATHINFO_EXTENSION);
            $nombreArchivo = $marca . "." . date("G").date("i").date("s");    
            $path = "./neumaticos/imagenes/" . $nombreArchivo . "." . $tipoArchivo;
            $uploadOk = TRUE;

            //VERIFICO QUE EL ARCHIVO NO EXISTA
            $array_extensiones = array("jpg", "jpeg", "gif", "png");
            for ($i = 0; $i < count($array_extensiones); $i++)
            {
                $nombre_archivo = "./neumaticos/imagenes/" . $nombreArchivo . "." . $array_extensiones[$i];
                if (file_exists($nombre_archivo)) 
                {
                    unlink($nombre_archivo);
                    break;
                }
            }

            //VERIFICO EL TAMAÑO MAXIMO QUE PERMITO SUBIR
            if ($_FILES["foto"]["size"] > 1000000) {
                $uploadOk = FALSE;
            }

            //OBTIENE EL TAMAÑO DE UNA IMAGEN, SI EL ARCHIVO NO ES UNA
            //IMAGEN, RETORNA FALSE
            $esImagen = getimagesize($_FILES["foto"]["tmp_name"]);

            if ($esImagen) 
            {
                if (
                    $tipoArchivo != "jpg" && $tipoArchivo != "jpeg" && $tipoArchivo != "gif"
                    && $tipoArchivo != "png"
                ) {
                    //echo "Solo son permitidas imagenes con extension JPG, JPEG, PNG o GIF.";
                    $uploadOk = FALSE;
                }
            }

            //VERIFICO SI HUBO ALGUN ERROR, CHEQUEANDO $uploadOk
            if ($uploadOk === FALSE) 
            {
                //echo "<br/>NO SE PUDO SUBIR EL ARCHIVO.";
                $path = "";
            }
	    }

	    return $path;
    }

    static function guardarImagen(string $path): bool
    {
        if(! isset($_FILES["foto"]))
        {
            return false;
        }
        return move_uploaded_file($_FILES["foto"]["tmp_name"], $path);
    }


}


?>