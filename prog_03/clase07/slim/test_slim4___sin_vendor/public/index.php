<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

//require __DIR__ . '/../src/app/app.php';

//al dir q vamos a usar
require __DIR__ . '/../vendor/autoload.php';

//genera el obj que permite comunicarme
$app = AppFactory::create();

//verbo peticion a raiz, similar a node
$app->get('/', function (Request $request, Response $response, $args){
    $response->getBody()->write("Hello world!!!!");
    return $response;
});

//levanta el servico de la api
$app->run();

/*
<VirtualHost *:80>
    ServerAdmin administrator@mail.com
    DocumentRoot "C:/xampp/htdocs/slim/test_slim4___sin_vendor/public"
    ServerName apitestslim4
    ErrorLog "logs/api_slim4-error.log"
    CustomLog "logs/api_slim4-access.log" common
</VirtualHost>
*/
