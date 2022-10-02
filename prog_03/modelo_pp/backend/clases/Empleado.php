<?php

namespace Borquez;
require_once("./clases/ICRUD.php");
require_once("./clases/Usuario.php");
use PDO;
use PDOException;
use stdClass;

class Empleado extends Usuario implements ICRUD
{
    public $foto;
    public $sueldo;

    public static function TraerTodosF()
    {
        $clave="";
        $usuario="root";
        $retorno = array();

        try
        {
            $db = new PDO('mysql:host=localhost;dbname=usuarios_test;charset=utf8', $usuario, $clave);
            $consulta = $db->prepare("SELECT id, correo, clave, nombre, id_perfil, foto, sueldo FROM empleados");
            $consulta->execute();

            while($obj = $consulta->fetchObject())
            {
                $empleado = new Empleado();
                $empleado->id = $obj->id;
                $empleado->nombre = $obj->nombre;
                $empleado->clave = $obj->clave;
                $empleado->correo = $obj->correo;
                $empleado->id_perfil = $obj->id_perfil;

                $empleado->foto = $obj->foto;
                $empleado->sueldo = $obj->sueldo;

                array_push($retorno, $empleado);
            }

            $consulta = $db->prepare("SELECT id, descripcion FROM perfiles");
            $consulta->execute();

            while($obj = $consulta->fetchObject())
            {
                foreach($retorno as $empleado)
                {
                    if($obj->id == $empleado->id_perfil)
                    {
                        $empleado->perfil = $obj->descripcion;
                    }
                }
            }
        }
        catch(PDOException)
        {
            $retorno = null;
        }

        return $retorno;
    }


    public function AgregarF()
    {
        $clave="";
        $usuario="root";
        $retorno = false;
                                                                                //  His / dmY
        $pathFoto = "./empleados/fotos/".$this->nombre.".".$this->id_perfil.".".date("His").".".pathinfo($this->foto,PATHINFO_EXTENSION);
        $this->foto = $pathFoto;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $pathFoto);

        try
        {
            $db = new PDO('mysql:host=localhost;dbname=usuarios_test;charset=utf8', $usuario, $clave);
            $consulta = $db->prepare("INSERT INTO empleados (correo, clave, nombre, id_perfil, foto, sueldo)"
                                    ."VALUES(:correo, :clave, :nombre, :id_perfil, :foto, :sueldo)");
            
            //$consulta->bindValue(":id",$this->id,PDO::PARAM_INT);
            $consulta->bindValue(":nombre",$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(":correo",$this->correo,PDO::PARAM_STR);
            $consulta->bindValue(":clave",$this->clave,PDO::PARAM_STR);
            $consulta->bindValue(":id_perfil",$this->id_perfil,PDO::PARAM_INT);
            $consulta->bindValue(":foto",$pathFoto,PDO::PARAM_STR);
            $consulta->bindValue(":sueldo",$this->sueldo,PDO::PARAM_INT);
            
            $consulta->execute();
            $retorno = true;
        }
        catch(PDOException)
        {
            return $retorno;
        }

        return $retorno;
    }


    public function ModificarF()
    {
        $clave="";
        $usuario="root";
        $retorno = false;

        //verificar si recibo foto para eliminar y luego subir denuevo
        //if(isset($_FILES["foto"]))...

        if(file_exists($this->foto))
        {
            unlink($this->foto);
        }
        $pathFoto = "./empleados/fotos/".$this->nombre.".".$this->id_perfil.".".date("His").".".pathinfo($this->foto,PATHINFO_EXTENSION);
        $this->foto = $pathFoto;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $pathFoto);

        try
        {
            $db = new PDO('mysql:host=localhost;dbname=usuarios_test;charset=utf8', $usuario,$clave);
            $consulta = $db->prepare("UPDATE empleados SET correo = :correo, clave = :clave,"
                                    ." nombre = :nombre, id_perfil = :id_perfil, foto = :foto, sueldo = :sueldo WHERE id = :id");
            
            $consulta->bindValue(":id",$this->id,PDO::PARAM_INT);
            $consulta->bindValue(":nombre",$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(":correo",$this->correo,PDO::PARAM_STR);
            $consulta->bindValue(":clave",$this->clave,PDO::PARAM_STR);
            $consulta->bindValue(":id_perfil",$this->id_perfil,PDO::PARAM_INT);
            $consulta->bindValue(":foto",$pathFoto,PDO::PARAM_STR);
            $consulta->bindValue(":sueldo",$this->sueldo,PDO::PARAM_INT);

            $consulta->execute();
            $retorno = true;
        }
        catch(PDOException)
        {
            return false;
        }

        return $retorno;
    }

    public static function EliminarF($id)
    {
        $clave="";
        $usuario="root";
        $retorno = false;

        try
        {
            $db = new PDO('mysql:host=localhost;dbname=usuarios_test;charset=utf8', $usuario, $clave);

            $consulta = $db->prepare("SELECT id, correo, clave, nombre, id_perfil, foto, sueldo FROM empleados WHERE id = :id");
            $consulta->bindValue(":id", $id, PDO::PARAM_INT);
            $consulta->execute();

            $consulta->setFetchMode(PDO::FETCH_INTO, new Empleado);

            foreach($consulta as $obj)
            {
                unlink($obj->foto);
            }

            $consulta = $db->prepare("DELETE FROM empleados WHERE id = :id");
            $consulta->bindValue(":id", $id, PDO::PARAM_INT);
            $consulta->execute();

            $retorno = true;
        }
        catch(PDOException)
        {
            return false;
        }

        return $retorno;
    }

}


?>


