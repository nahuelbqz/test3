<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

use Slim\Factory\AppFactory;
use \Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

///////////////////////////////////////////////////////////////////////////////////////////////////

require_once __DIR__ . '/../src/clases/MW.php';
require_once __DIR__ . "/../src/clases/auto.php";
require_once __DIR__ . "/../src/clases/usuario.php";
require_once __DIR__ . "/../src/clases/autentificadora.php";

//firma correcta para el standard -> (request, response, array) => response
/*
$app->get('/', function (Request $request, Response $response, array $args) : Response {  

    $response->getBody()->write("GET => Bienvenido!!! a SlimFramework 4 desde modelosp");
    return $response;
});
*/

///////////////////////      USUARIO     ////////////////////////////
$app->post('/usuarios', Usuario::class . ':AgregarUno')
    ->add(\MW::class . '::VerificarCorreoNoExiste')
    ->add(\MW::class . '::VerificarCorreoClaveVacios')
    ->add(\MW::class . ':VerificarCorreoClaveSeteado');

//(GET) Listado de usuarios.
$app->get('/', \Usuario::class . ':TraerTodos');


///////////////////////     AUTO   ///////////////////////////////////
$app->post('/', Auto::class . ':agregarUno')
    ->add(\MW::class . ':VerificarPrecioYColor');

//(GET) LISTADO de autos.
//$app->get('/autos', \Auto::class . ':TraerTodos');


/////////////////////     LOGIN USUARIO     /////////////////////////
$app->post("/login", \Usuario::class . ':VerificarUsuario')
    ->add(\MW::class . ':VerificarClaveCorreoExistenEnBD')
    ->add(\MW::class . '::VerificarCorreoClaveVacios')
    ->add(\MW::class . ':VerificarCorreoClaveSeteado');

//(GET) Se envía el JWT → token (en el header) y se verifica
$app->get("/login", \Usuario::class . ':VerificarTokenJWT');



///////////////////////////   DELETE Y PUT    //////////////////////////////////////
$app->delete("/", \Auto::class . ':BorrarUno')
    ->add(\MW::class . ':VerificarPropietario')
    ->add(\mw::class . ':VerificarTokenJWTmw');

$app->put("/", \Auto::class . ':ModificarUno')
    ->add(\MW::class . ':VerificarEncargado')
    ->add(\mw::class . ':VerificarTokenJWTmw');


/////////////////////////       GRUPO       //////////////////////////////////////////////////
$app->group('/autos', function (RouteCollectorProxy $grupo){
    $grupo->get('/', \Auto::class . ':TraerTodos')
      ->add(\MW::class . ':MostrarDatosDeAutosAEncargado')
      ->add(\MW::class . ':MostrarDatosDeAutosAEmpleado')
      ->add(\MW::class . ':MostrarDatosDeAutosAPropietario');
});


try 
{
    //CORRE LA APLICACIÓN.
    $app->run();
}catch (Exception $e){
    // Muestro mensaje de error
    die(json_encode(array("status" => "failed", "message" => "This action is not allowed")));
}