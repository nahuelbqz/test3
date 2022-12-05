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

require_once __DIR__ . '/../src/MW.php';
require_once __DIR__ . "/../src/juguete.php";
require_once __DIR__ . "/../src/usuario.php";

//require_once __DIR__ . "/../src/clases/autentificadora.php";


//////////////        USUARIO    /////////////////////////////////////////
//(GET) Listado de usuarios.
$app->get('/', \Usuario::class . ':TraerTodos');


/////////////////    JUGUETE       ////////////////////////////////////////////////
$app->post('/', \Juguete::class . ':AgregarUno');

//(GET) Listado de juguetes.
$app->get('/juguetes', \Juguete::class . ':TraerTodos');


/////////////////////     LOGIN USUARIO     /////////////////////////
$app->post("/login", \Usuario::class . ':VerificarUsuario')
    ->add(\MW::class . ':VerificarClaveCorreoExistenEnBD')
    ->add(\MW::class . '::VerificarCorreoClaveVacios');

//(GET) Se envía el JWT → token (en el header) y se verifica
$app->get("/login", \Usuario::class . ':VerificarTokenJWT');


/////////////////////////       GRUPO TOYS    //////////////////////////////////////////////////
$app->group('/toys', function (RouteCollectorProxy $grupo){
    $grupo->delete('/{id_juguete}', \Juguete::class . ':BorrarUno');

    $grupo->post('/', \Juguete::class . ':ModificarUno')
    ->add(\MW::class . ':VerificarTokenJWTmw');
    
})->add(\MW::class . ':VerificarTokenJWTmw');


$app->group('/tablas', function (RouteCollectorProxy $grupo){
    $grupo->get('/usuarios', \Usuario::class . ':TraerTodos')
    ->add(\MW::class . '::ListarTablaSinClave');

    ///
    ///
    
  })->add(\MW::class . ':VerificarTokenJWTmw');
  


/*
$app->run();
*/

try 
{
    //CORRE LA APLICACIÓN.
    $app->run();
}catch (Exception $e){
    // Muestro mensaje de error
    die(json_encode(array("status" => "failed", "message" => "This action is not allowed")));
}
