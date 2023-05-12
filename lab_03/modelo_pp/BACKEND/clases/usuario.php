<?php

namespace Borquez;

require_once("./clases/IBM.php");
use PDO;
use PDOException;
use stdClass;

class Usuario
{
    public $id;
    public $nombre;
    public $correo;
    public $clave;
    public $id_perfil;
    public $perfil;
    
    /*
    public function __construct($nombre,$correo,$clave,$id=0,$id_perfil=0,$perfil=""){
        $this->id=$id;
        $this->nombre=$nombre;
        $this->correo=$correo;
        $this->clave=$clave;
        $this->id_perfil=$id_perfil;
        $this->perfil=$perfil;
    }
    */

    public function ToJSON()
    {
        $obj = new stdClass();
        $obj->nombre = $this->nombre;
        $obj->correo = $this->correo;
        $obj->clave = $this->clave;
        
        return json_encode($obj);
    }

    public function GuardarEnArchivo()
    {
        $respuesta = new stdClass();
        $path = "./clases/archivos/usuarios.json";
        
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
            $respuesta->exito = TRUE;
            $respuesta->mensaje = "Se a Guardado Correctamente";
            //array_push($retorno,$exito);
        }
        else
        {
            $respuesta->exito=false;
            $respuesta->mensaje="NO se a Guardado Correctamente";
            //array_push($retorno,$exito);
            //array_push($retorno,$mensaje);
        }

        return json_encode($respuesta);
    }


    public static function TraerTodosJSON()
    {
        $retorno = array();
        $json = file_get_contents("./clases/archivos/usuarios.json");
        $auxArrayDecodeado = json_decode($json);
        //var_dump($auxArrayDecodeado);die();

        foreach($auxArrayDecodeado as $obj)
        {
            $usuario = new Usuario();
            $usuario->nombre = $obj->nombre;
            $usuario->correo = $obj->correo;
            $usuario->clave = $obj->clave;
            //$cadena=$usuario->ToJSON();
            array_push($retorno, $usuario);
        }

        return $retorno;
    }


    public function Agregar() : bool
    {
        $usuario="root";
        $clave="";
        $retorno=false;

        try
        {
            $db = new PDO('mysql:host=localhost;dbname=usuarios_test;charset=utf8', $usuario, $clave);
            $consulta = $db->prepare("INSERT INTO usuarios (correo, clave, nombre, id_perfil)"
                                    ."VALUES(:correo, :clave, :nombre, :id_perfil)");
            
            //$consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
            $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(":correo", $this->correo, PDO::PARAM_STR);
            $consulta->bindValue(":clave", $this->clave, PDO::PARAM_STR);
            $consulta->bindValue(":id_perfil", $this->id_perfil, PDO::PARAM_INT);

            $consulta->execute();
            $retorno = true;
        }
        catch(PDOException)
        {
            return $retorno;
        }

        return $retorno;
    }


    public static function TraerTodos()
    {
        $clave="";
        $usuario="root";
        $arrayUsuariosRetorno = array();

        try
        {
            $db = new PDO('mysql:host=localhost;dbname=usuarios_test;charset=utf8', $usuario,$clave);
            $consulta = $db->prepare("SELECT id, correo, clave, nombre, id_perfil FROM usuarios");
            $consulta->execute();
            
            while($obj = $consulta->fetchObject())
            {
                $usuarioObj = new Usuario();
                $usuarioObj->id = $obj->id;
                $usuarioObj->nombre = $obj->nombre;
                $usuarioObj->clave = $obj->clave;
                $usuarioObj->correo = $obj->correo;
                $usuarioObj->id_perfil = $obj->id_perfil;

                array_push($arrayUsuariosRetorno, $usuarioObj);
            }

            $consulta = $db->prepare("SELECT id, descripcion FROM perfiles");
            $consulta->execute();
            
            while($perfil = $consulta->fetchObject())
            {
                foreach($arrayUsuariosRetorno as $usuario)
                {
                    if($perfil->id == $usuario->id_perfil)
                    {
                        $usuario->perfil = $perfil->descripcion;
                    }
                }
            }
        }
        catch(PDOException)
        {
            $arrayUsuariosRetorno = null;
        }

        return $arrayUsuariosRetorno;
    }


    public static function TraerUno($params):Usuario|NULL
    {
        //$retorno = new Usuario();
        $usuarios = Usuario::TraerTodosJSON();
        
        //var_dump($usuarios);die();
        foreach($usuarios as $usuario)
        {
        
            if($usuario->clave == $params->clave && $usuario->correo == $params->correo){

                $retorno = new Usuario();
                $retorno->nombre = $usuario->nombre;
                $retorno->correo = $usuario->correo;
                $retorno->clave = $usuario->clave;
                
                break;
            }
            else
            {
                $retorno = NULL;
            }
        }
        
        return $retorno;
    }


    ////////////   IBM    //////////////

    public function Modificar():bool
    {
        $clave="";
        $usuario="root";
        $retorno=false;
        

        try
        {
            $db = new PDO('mysql:host=localhost;dbname=usuarios_test;charset=utf8', $usuario, $clave);
            $consulta = $db->prepare("UPDATE usuarios SET correo = :correo, clave = :clave,"
                                    ." nombre = :nombre, id_perfil = :id_perfil WHERE id = :id");
            
            $consulta->bindValue(":id",$this->id,PDO::PARAM_INT);
            $consulta->bindValue(":nombre",$this->nombre,PDO::PARAM_STR);
            $consulta->bindValue(":correo",$this->correo,PDO::PARAM_STR);
            $consulta->bindValue(":clave",$this->clave,PDO::PARAM_STR);
            $consulta->bindValue(":id_perfil",$this->id_perfil,PDO::PARAM_INT);

            $consulta->execute();
            $retorno = true;
        }
        catch(PDOException)
        {
            return false;
        }

        return $retorno;
    }
    
    public static function Eliminar($id):bool
    {
        $clave="";
        $usuario="root";
        $retorno=false;

        try
        {
            $db = new PDO('mysql:host=localhost;dbname=usuarios_test;charset=utf8', $usuario,$clave);
            $consulta = $db->prepare("DELETE FROM usuarios WHERE id = :id");
            
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