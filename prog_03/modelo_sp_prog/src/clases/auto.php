<?php

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

require_once "accesoDatos.php";
require_once "Usuario.php";
require_once __DIR__ . "/autentificadora.php";

class Auto
{
    public string $color;
    public string $marca;
    public int $precio;
    public string $modelo;


    public function AgregarUno(Request $request, Response $response, array $args) : Response
    {
        $parametros = $request->getParsedBody();
        
        $obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "NO se pudo agregar el auto!!";
        $obj_respuesta->status = 418;

        if(isset($parametros["auto"]))
        {
            $obj = json_decode($parametros["auto"]);

            $auto = new Auto();
            $auto->color = $obj->color;
            $auto->marca = $obj->marca;
            $auto->precio = $obj->precio;
            $auto->modelo = $obj->modelo;

            $id_agregado = $auto->AgregarAuto();
            $auto->id = $id_agregado;

            if($id_agregado)
            {
                $obj_respuesta->exito = true;
                $obj_respuesta->mensaje = "Auto Agregado correctamente";
                $obj_respuesta->status = 200;
            }
        }

        $newResponse = $response->withStatus($obj_respuesta->status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodos(Request $request, Response $response, array $args): Response
    {
        $obj_respuesta = new stdClass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "NO se encontraron autos";
        $obj_respuesta->dato = "{}";
        $obj_respuesta->status = 424;

        $autos = Auto::TraerAutos();

        if(count($autos))
        {
            $obj_respuesta->exito = true;
            $obj_respuesta->mensaje = "Autos encontrados correctamente";
            $obj_respuesta->dato = json_encode($autos);
            $obj_respuesta->status = 200;
        }

        $newResponse = $response->withStatus($obj_respuesta->status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }


    public function BorrarUno(Request $request, Response $response, array $args): Response
    {
        $obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "NO se pudo borrar el auto!!";
        $obj_respuesta->status = 418;

        if(isset($request->getHeader("token")[0]) &&
            isset($request->getHeader("id_auto")[0])
        ){
            $token = $request->getHeader("token")[0];
            $id = $request->getHeader("id_auto")[0];

            $datos_token = Autentificadora::obtenerPayLoad($token);
            $usuario_token = $datos_token->payload->data;
            $perfil_usuario = $usuario_token->perfil;

            if($perfil_usuario == "propietario")
            {
                if(Auto::BorrarAuto($id))
                {
                    $obj_respuesta->exito = true;
                    $obj_respuesta->mensaje = "Auto Borrado correctamente";
                    $obj_respuesta->status = 200;
                }
                else
                {
                    $obj_respuesta->mensaje = "El Auto NO existe en el listado!!";
                }
            }
            else
            {
                $obj_respuesta->mensaje = "Usuario NO AUTORIZADO para realizar esta accion. {$usuario_token->nombre} - {$usuario_token->apellido} - {$usuario_token->perfil}";
            }
        }

        $newResponse = $response->withStatus($obj_respuesta->status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }


    public function ModificarUno(Request $request, Response $response, array $args): Response
    {
        $obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No se pudo modificar el auto";
        $obj_respuesta->status = 418;

        if(isset($request->getHeader("token")[0]) &&
            isset($request->getHeader("id_auto")[0]) &&
            isset($request->getHeader("auto")[0])
        ){
            $token = $request->getHeader("token")[0];
            $id = $request->getHeader("id_auto")[0];
            $obj_json = json_decode($request->getHeader("auto")[0]);

            $datos_token = Autentificadora::obtenerPayLoad($token);
            $usuario_token = $datos_token->payload->data;
            $perfil_usuario = $usuario_token->perfil;

            if($perfil_usuario == "encargado") 
            {
                if($auto = Auto::TraerAutoPorId($id))
                {
                    $auto->color = $obj_json->color;
                    $auto->marca = $obj_json->marca;
                    $auto->precio = $obj_json->precio;
                    $auto->modelo = $obj_json->modelo;
                    if($auto->ModificarAuto())
                    {
                        $obj_respuesta->exito = true;
                        $obj_respuesta->mensaje = "Auto Modificado correctamente";
                        $obj_respuesta->status = 200;
                    }

                }
                else 
                {
                    $obj_respuesta->mensaje = "El Auto NO EXISTE en el listado!!!";
                }
            } 
            else 
            {
                $obj_respuesta->mensaje = "Usuario NO AUTORIZADO para realizar la accion. {$usuario_token->nombre} - {$usuario_token->apellido} - {$usuario_token->perfil}";
            }
        }

        $newResponse = $response->withStatus($obj_respuesta->status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////  METODOS DE CONSULTAS SQL   ///////////////////////////////////////////

    public function AgregarAuto()
    {
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();

        $consulta = $accesoDatos->retornarConsulta(
            "INSERT INTO autos (color, marca, precio, modelo) 
                        VALUES(:color, :marca, :precio, :modelo)"
        );

        $consulta->bindValue(":color", $this->color, PDO::PARAM_STR);
        $consulta->bindValue(":marca", $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(":precio", $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(":modelo", $this->modelo, PDO::PARAM_INT);
        $consulta->execute();

        return $accesoDatos->retornarUltimoIdInsertado();
    }

    public static function TraerAutos()
    {
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();

        $consulta = $accesoDatos->retornarConsulta("SELECT * FROM autos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Auto");
    }

    public static function TraerAutoPorId(int $id)
    {
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
        $consulta = $accesoDatos->retornarConsulta(
            "SELECT * FROM autos 
             WHERE id = :id"
        );
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        $auto = $consulta->fetchObject('Auto');

        return $auto;
    }

    public static function BorrarAuto(int $id)
    {
        $retorno = false;
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
        $consulta = $accesoDatos->retornarConsulta("DELETE FROM autos WHERE id = :id");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        $total_borrado = $consulta->rowCount(); // verifico las filas afectadas por la consulta
        if($total_borrado == 1) 
        {
            $retorno = true;
        }

        return $retorno;
    }

    public function ModificarAuto()
    {
        $retorno = false;

        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();

        $consulta = $accesoDatos->retornarConsulta(
            "UPDATE autos
             SET color = :color, marca = :marca, precio = :precio, modelo = :modelo
             WHERE id = :id"
        );

        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":color", $this->color, PDO::PARAM_STR);
        $consulta->bindValue(":marca", $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(":precio", $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(":modelo", $this->modelo, PDO::PARAM_INT);
        $consulta->execute();

        $total_modificado = $consulta->rowCount(); // verifico las filas afectadas por la consulta
        if($total_modificado == 1) 
        {
            $retorno = true;
        }

        return $retorno;
    }
    

}

?>