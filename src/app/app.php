<?php

use DI\Container;

use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';

$cont_aux = new \DI\Container();

AppFactory::setContainer($cont_aux);

$app = AppFactory::create();

$container = $app->getContainer();

include_once 'config.php';

$app->add(new Tuupola\Middleware\JwtAuthentication([
    "secure" => false,
    //"path" => ["/cliente"], // Todo lo que queremos que funcione
   //"ignore" => ['/sesion','/cliente','/artefacto'], // Todo lo que queremos que ignore
   "ignore" => ['/sesion','/artefacto','/usuario'],
    //"logger" => $logger,
    ///usuario/passw/cambio
    //"ignore" => ['/usuario/passw/cambio'],
    //"ignore" => ['/sesion/iniciar'],
   // "ignore" => ['/sesion/iniciar'],
    //"secret" => 'Alguna Clave',
    "secret" => $container->get('clave'),
    "Algorithm" => ["HS256", "HS384"]
]));
 

include_once 'routes.php';
include_once 'conexion.php';


$app->run();
