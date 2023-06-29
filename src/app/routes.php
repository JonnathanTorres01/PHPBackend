<?php

namespace App\controller;

use Slim\Routing\RouteCollectorProxy;
//require __DIR__ . '/../controller/Artefacto.php';

$app->group('/artefacto', function(RouteCollectorProxy $artefacto)
{ 
    $artefacto->get('/{pagina}/{limite}', Artefacto::class . ':filtrar');//Listo
    $artefacto->get('',  Artefacto::class . ':numRegs');//Listo
    $artefacto->get('/{id}',  Artefacto::class . ':buscar');//Listo
    $artefacto->post('', Artefacto::class . ':crear'); //Listo
    $artefacto->put('/{id}', Artefacto::class . ':editar');//Listo
    $artefacto->patch('/{id}', Artefacto::class . ':cambiarPropietario'); //Listo
    $artefacto->delete('/{id}', Artefacto::class . ':eliminar');//Listo
});


$app->group('/cliente', function(RouteCollectorProxy $cliente)
{
    $cliente->get('/{pagina}/{limite}', Cliente::class . ':filtrar');
    $cliente->get('',  Cliente::class . ':numRegs');

    //$cliente->get('/{id}',  Cliente::class . ':buscar');
    
    $cliente->get('/{id}',  Cliente::class . ':buscarUno');

    $cliente->post('', Cliente::class . ':crear');
    $cliente->put('/{id}', Cliente::class . ':editar');// Listo
    $cliente->patch('/{id}', Cliente::class . ':cambiarPropietario');
    $cliente->delete('/{id}', Cliente::class . ':eliminar');// Listo
});


$app->group('/usuario', function(RouteCollectorProxy $usuario)
{
 
    $usuario->patch('/rol/{id}',  Usuario::class . ':cambiarRol');
  
    $usuario->group('/passw', function(RouteCollectorProxy $passw){
        $passw->patch('/cambio/{id}', Usuario::class . ':cambiarPassw');
        $passw->patch('/reset/{id}', Usuario::class . ':resetPassw'); 
    });

});



$app->group('/sesion', function(RouteCollectorProxy $sesion)
{
    $sesion->patch('/iniciar/{id}',  Sesion::class . ':iniciar');
    $sesion->patch('/cerrar/{id}',  Sesion::class . ':cerrar');
    $sesion->patch('/refrescar/{id}',  Sesion::class . ':refrescar');
});

