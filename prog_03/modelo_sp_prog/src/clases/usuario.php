<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;


require_once "accesoDatos.php";
require_once "autentificadora.php";

class Usuario
{
    public int $id;
    public string $correo;
    public string $clave;
    public string $nombre;
    public string $apellido;
    public string $perfil;
    public string $foto;
    protected static PDO $objetoPDO;

    public function __construct()
    {
        
    }

    public function AgregarUno(Request $request, Response $response, array $args): Response
    {
        $parametros = $request->getParsedBody();

        $obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "NO se pudo agregar el usuario!!";
        $obj_respuesta->status = 418;

        if(isset($parametros["usuario"]))
        {
            $obj = json_decode($parametros["usuario"]);

            $usuario = new Usuario();
            $usuario->correo = $obj->correo;
            $usuario->clave = $obj->clave;
            $usuario->nombre = $obj->nombre;
            $usuario->apellido = $obj->apellido;
            $usuario->perfil = $obj->perfil;
            $usuario->foto = "";

            $id_agregado = $usuario->AgregarUsuario();
            $usuario->id = $id_agregado;

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

                $foto = $destino . $usuario->correo . "_" . $id_agregado . "." . $extension[0];
                $archivos['foto']->moveTo("." . $foto); // Ojo agregue un punto .
                $usuario->foto = $foto;
                $usuario->ModificarUsuario();
            }
            //#####################################################

            if($id_agregado)
            {
                $obj_respuesta->exito = true;
                $obj_respuesta->mensaje = "Usuario agregado correctamente";
                $obj_respuesta->status = 200;
            }
        }

        $newResponse = $response->withStatus($obj_respuesta->status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));

        return $newResponse->withHeader('Content-Type', 'application/json');
    }
/*
PARA TEST
{ "correo" : "pepe123@gmail.com","clave": "1234","nombre": "pepe","apellido": "pepito","perfil": "administrador"}
*/

    public function TraerTodos(Request $request, Response $response, array $args): Response
    {
        $obj_respuesta = new stdClass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No hay usuarios";
        $obj_respuesta->dato = "{}";
        $obj_respuesta->status = 424;

        $usuarios = Usuario::TraerUsuarios();

        if(count($usuarios))
        {
            $obj_respuesta->exito = true;
            $obj_respuesta->mensaje = "Usuarios encontrados";
            $obj_respuesta->dato = json_encode($usuarios);
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


    //////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////  METODOS DE CONSULTAS SQL   ///////////////////////////////////////////

    public function AgregarUsuario()
    {
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();

        $consulta = $accesoDatos->retornarConsulta(
            "INSERT INTO usuarios (correo, clave, nombre, apellido, perfil, foto) 
                            VALUES(:correo, :clave, :nombre, :apellido, :perfil, :foto)"
        );

        $consulta->bindValue(":correo", $this->correo, PDO::PARAM_STR);
        $consulta->bindValue(":clave", $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":apellido", $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(":perfil", $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(":foto", $this->foto, PDO::PARAM_STR);
        $consulta->execute();

        return $accesoDatos->retornarUltimoIdInsertado();
    }

    public function ModificarUsuario()
    {
        $retorno = false;

        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();

        $consulta = $accesoDatos->retornarConsulta(
            "UPDATE usuarios 
                SET correo = :correo, clave = :clave, nombre = :nombre,
                    apellido = :apellido, perfil = :perfil, foto = :foto
                WHERE id = :id"
        );

        $consulta->bindValue(":id", $this->id, PDO::PARAM_INT);
        $consulta->bindValue(":correo", $this->correo, PDO::PARAM_STR);
        $consulta->bindValue(":clave", $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(":nombre", $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(":apellido", $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(":perfil", $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(":foto", $this->foto, PDO::PARAM_STR);
        $consulta->execute();

        $total_modificado = $consulta->rowCount(); // verifico las filas afectadas por la consulta
        if($total_modificado == 1)
        {
            $retorno = true;
        }

        return $retorno;
    }


    public static function TraerUsuarios()
    {
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
        $consulta = $accesoDatos->retornarConsulta("SELECT * FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");
    }

    //DEVUELVE EL OBJ SI SE ENCONTRO POR IGUAL CORREO Y CLAVE, FALSE CASO CONTRARIO
    public static function TraerUsuario($obj)
    {
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
        $consulta = $accesoDatos->retornarConsulta(
                "SELECT * FROM usuarios
                WHERE correo = :correo AND clave = :clave"
        );
        $consulta->bindValue(":correo", $obj->correo, PDO::PARAM_STR);
        $consulta->bindValue(":clave", $obj->clave, PDO::PARAM_STR);
        $consulta->execute();

        $usuario = $consulta->fetchObject('Usuario');

        return $usuario;
    }
    
    public static function TraerUsuarioPorCorreo($correo)
    {
        $accesoDatos = AccesoDatos::obtenerObjetoAccesoDatos();
        $consulta = $accesoDatos->retornarConsulta(
            "SELECT * FROM usuarios
             WHERE correo = :correo"
        );
        $consulta->bindValue(":correo", $correo, PDO::PARAM_STR);
        $consulta->execute();

        $usuario = $consulta->fetchObject('Usuario');

        return $usuario;
    }


}//Clase usuario

