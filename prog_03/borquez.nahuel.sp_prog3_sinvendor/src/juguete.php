<?php

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

require_once "usuario.php";
require_once "accesoDatos.php";
require_once __DIR__ . "/autentificadora.php";

class Juguete
{
    public string $id;
    public string $marca;
    public int $precio;
    public string $path_foto;

    protected static PDO $objetoPDO;

    public function __construct()
    {
        
    }

    public function AgregarUno(Request $request, Response $response, array $args): Response
    {
        $parametros = $request->getParsedBody();

        $obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "NO se pudo agregar el juguete!!";
        $obj_respuesta->status = 418;

        if(isset($parametros["juguete_json"]))
        {
            $obj = json_decode($parametros["juguete_json"]);

            $juguete = new Juguete();
            //$juguete->id = $obj->id;
            $juguete->marca = $obj->marca;
            $juguete->precio = $obj->precio;
            $juguete->path_foto = "";

            $id_agregado = $juguete->AgregarJuguete();
            $juguete->id = $id_agregado;

            $foto = "";
            //#####################################################
            // Guardado de foto/archivo
            if(count($request->getUploadedFiles()))
            {
                $archivos = $request->getUploadedFiles();
                $destino = "./src/fotos/";

                $nombreAnterior = $archivos['foto']->getClientFilename();
                $extension = explode(".", $nombreAnterior);
                $extension = array_reverse($extension);

                $foto = $destino . $juguete->marca . "." . $extension[0];
                $archivos['foto']->moveTo("." . $foto); // Ojo agregue un punto .
                $juguete->path_foto = $foto;

                $juguete->ModificarJuguete();
            }
            //#####################################################

            if($id_agregado)
            {
                $obj_respuesta->exito = true;
                $obj_respuesta->mensaje = "Juguete agregado correctamente";
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
        $obj_respuesta->mensaje = "No hay juguetes en la lista";
        $obj_respuesta->dato = "{}";
        $obj_respuesta->status = 424;

        $juguetes = Juguete::TraerJuguetes();

        if(count($juguetes))
        {
            $obj_respuesta->exito = true;
            $obj_respuesta->mensaje = "Juguetes encontrados correctamente";
            $obj_respuesta->dato = json_encode($juguetes);
            $obj_respuesta->status = 200;
        }

        $newResponse = $response->withStatus($obj_respuesta->status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));
        
        return $newResponse->withHeader('Content-Type', 'application/json');
    }


    public function VerificarUsuario(Request $request, Response $response, array $args): Response
    {
        $arrayDeParametros = $request->getParsedBody();

        $obj_respuesta = new stdClass();
        $obj_respuesta->exito = false;
        $obj_respuesta->jwt = null;
        $obj_respuesta->status = 403;

        if(isset($arrayDeParametros["user"]))
        {
            $obj = json_decode($arrayDeParametros["user"]);

            if($usuario = Usuario::TraerUsuario($obj))
            {
                $data = new stdClass();
                $data->id = $usuario->id;
                $data->correo = $usuario->correo;
                $data->nombre = $usuario->nombre;
                $data->apellido = $usuario->apellido;
                $data->perfil = $usuario->perfil;
                $data->foto = $usuario->foto;

                $obj_respuesta->exito = true;               //tiempo exp 30s
                $obj_respuesta->jwt =  Autentificadora::crearJWT($data, 30);
                $obj_respuesta->status = 200;
            }
        }

        $contenidoAPI = json_encode($obj_respuesta);
        $response = $response->withStatus($obj_respuesta->status);
        $response->getBody()->write($contenidoAPI);

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function VerificarTokenJWT(Request $request, Response $response, array $args) : Response
    {
        $contenidoAPI = "";
        $obj_respuesta = new stdClass();
        $obj_respuesta->mensaje = "Token Invalido!";
        $obj_respuesta->status = 403;

        if(isset($request->getHeader("token")[0]))
        {
            $token = $request->getHeader("token")[0];

            $obj = Autentificadora::verificarJWT($token);

            if($obj->status == 200) //si el status del verificar es 200 todo OK, 403 fallo algo..
            {
                $obj_respuesta->mensaje = $obj->mensaje;
                $obj_respuesta->status = 200;
            }
        }
        
        $contenidoAPI = json_encode($obj_respuesta);

        $response = $response->withStatus($obj_respuesta->status);
        $response->getBody()->write($contenidoAPI);
        return $response->withHeader('Content-Type', 'application/json');
    }

    
    public function BorrarUno(Request $request, Response $response, array $args) : Response
    {
        $obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "NO se pudo borrar el juguete!!";
        $obj_respuesta->status = 418;
        
        if(isset($request->getHeader("token")[0]))
        {
            $token = $request->getHeader("token")[0];
            $id = $args['id_juguete'];

            $datos = Autentificadora::verificarJWT($token);
            /*
            si el token es OK puedo obtener payload y hacer cosas luego..
            $datos_token = Autentificadora::obtenerPayLoad($token);
            $usuario_token = $datos_token->payload->data;
            $perfil_usuario = $usuario_token->perfil;
            */
            if($datos->status == 200)
            {
                if(Juguete::BorrarJuguete($id))
                {
                    $obj_respuesta->exito = true;
                    $obj_respuesta->mensaje = "Juguete Borrado correctamente";
                    $obj_respuesta->status = 200;
                }
                else
                {
                    $obj_respuesta->mensaje = "El Juguete NO existe en el listado!!";
                }
            }
            else
            {
                $obj_respuesta->mensaje = "Token invalido al querer elimnar juguete!! ";
            }
        }

        $newResponse = $response->withStatus($obj_respuesta->status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }


    public function ModificarUno(Request $request, Response $response, array $args): Response
    {
        $arrayDeParametros = $request->getParsedBody();

        $obj_respuesta = new stdClass();        
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No se pudo modificar el juguete";
        $obj_respuesta->status = 418;
        
        if(isset($arrayDeParametros["juguete"]) && isset($request->getHeader("token")[0]))
        {
            $obj = json_decode($arrayDeParametros["juguete"]);
            $token = $request->getHeader("token")[0];

            $datos = Autentificadora::verificarJWT($token);
            /*
            si el token es OK puedo obtener payload y hacer cosas luego..
            $datos_token = Autentificadora::obtenerPayLoad($token);
            $usuario_token = $datos_token->payload->data;
            $perfil_usuario = $usuario_token->perfil;

            ej:
            $datos_token = Autentificadora::obtenerPayLoad($token);
            $usuario_token = $datos_token->payload->usuario;
            $perfil_usuario = $usuario_token->perfil;// 1- propietario, 2- supervisor, 3- empleado

            if ($perfil_usuario == "supervisor")
            {
                si es supervisor hacer cosas...
            }
            else
            {
                $obj_respuesta->mensaje = "Usuario no autorizado para realizar la accion, debe ser supervisor. {$usuario_token->nombre} - {$usuario_token->apellido} - {$usuario_token->perfil}";
            }
            */
            if($datos->status == 200)
            {
                if($juguete = Juguete::TraerJuguetePorId($obj->id_juguete))
                {
                    $juguete->marca = $obj->marca;
                    $juguete->precio = $obj->precio;
                    $juguete->path_foto = "";

                    $foto = "";
                    //#####################################################
                    // Guardado de foto/archivo
                    if(count($request->getUploadedFiles()))
                    {
                        $archivos = $request->getUploadedFiles();
                        $destino = "./src/fotos/";
        
                        $nombreAnterior = $archivos['foto']->getClientFilename();
                        $extension = explode(".", $nombreAnterior);
                        $extension = array_reverse($extension);
        
                        $foto = $destino . $obj->marca ."_modificacion"."." . $extension[0];
                        $archivos['foto']->moveTo("." . $foto); // Ojo agregue un punto .
                    
                        $juguete->path_foto = $foto;
                    }
                    //#####################################################
                    
                    if($juguete->ModificarJuguete())
                    {
                        $obj_respuesta->exito = true;
                        $obj_respuesta->mensaje = "Juguete Modificado correctamente";
                        $obj_respuesta->status = 200;
                    }
                }
                else 
                {
                    $obj_respuesta->mensaje = "El juguete NO EXISTE en el listado!!!";
                }
            } 
            else
            {
                $obj_respuesta->mensaje = "TOKEN INVALIDO O EXPIRADO!!!";
            }
        }
    
        $newResponse = $response->withStatus($obj_respuesta->status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////  METODOS DE CONSULTAS SQL   ///////////////////////////////////////////

    public function AgregarJuguete()
    {
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();

        $consulta = $accesoDatos->retornarConsulta(
            "INSERT INTO juguetes (marca, precio, path_foto) 
                            VALUES(:marca, :precio, :path_foto)"
        );

        $consulta->bindValue(":marca", $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(":precio", $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(":path_foto", $this->path_foto, PDO::PARAM_STR);

        $consulta->execute();

        return $accesoDatos->retornarUltimoIdInsertado();
    }

    //MODIFICAR
    public function ModificarJuguete()
    {
        $retorno = false;

        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();

        $consulta = $accesoDatos->retornarConsulta(
            "UPDATE juguetes 
                SET marca = :marca, precio = :precio, path_foto = :path_foto
                WHERE id = :id"
        );

        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":marca", $this->marca, PDO::PARAM_STR);
        $consulta->bindValue(":precio", $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(":path_foto", $this->path_foto, PDO::PARAM_STR);
        $consulta->execute();

        $total_modificado = $consulta->rowCount(); // verifico las filas afectadas por la consulta
        if($total_modificado == 1)
        {
            $retorno = true;
        }

        return $retorno;
    }

    /// BORRAR
    public static function BorrarJuguete(int $id)
    {
        $retorno = false;
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
        $consulta = $accesoDatos->retornarConsulta("DELETE FROM juguetes WHERE id = :id");
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        $total_borrado = $consulta->rowCount(); // verifico las filas afectadas por la consulta
        if($total_borrado == 1) 
        {
            $retorno = true;
        }

        return $retorno;
    }

    public static function TraerJuguetes()
    {
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
        $consulta = $accesoDatos->retornarConsulta("SELECT * FROM juguetes");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Juguete");
    }

    public static function TraerJuguetePorId(int $id)
    {
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
        $consulta = $accesoDatos->retornarConsulta(
            "SELECT * FROM juguetes 
             WHERE id = :id"
        );
        $consulta->bindValue(":id", $id, PDO::PARAM_INT);
        $consulta->execute();

        $juguete = $consulta->fetchObject('Juguete');

        return $juguete;
    }
    


}//Clase juguete


?>