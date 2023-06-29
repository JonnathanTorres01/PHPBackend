<?php

namespace App\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;
 
class Usuario extends AccesoBD{

    const RECURSO = "Usuario";


    private function autenticar($idUsuario, $passw){

        $datos = $this->buscarUsr(idUsuario: $idUsuario);

        return(($datos) && (password_verify($passw,$datos->passw))) ?
        ['rol' => $datos->rol] : null;

        //var_dump($datos); die();
    }

    public function cambiarRol(Request $request, Response $response, $args){

        $body = json_decode($request->getbody());
        
        $datos = $this->editarUsuario(idUsuario: $args['id'], rol: $body->rol);
        
        $status = $datos = true ? 200 : 404;

       return $response->withStatus($status);

    }

    public function cambiarPassw(Request $request, Response $response, $args){

        $body = json_decode($request->getbody(),1);
     
        $usuario =  $this->autenticar($args['id'], $body['passw']);

        


       //var_dump($usuario); die();

        if($usuario){

            $datos = $this->editarUsuario(idUsuario: $args['id'], passw: Hash::hash($body['passwN']));

            $status = 200;


        }else {
            $status = 401;
        }


                
       // $status = $datos = true ? 200 : 404;

       return $response->withStatus($status);

    }

    public function resetPassw(Request $request, Response $response, $args){ // Un administrador
        $body = json_decode($request->getbody());
        
        $datos = $this->editarUsuario(idUsuario: $args['id'], passw: Hash::hash($body->passw));
        
        $status = $datos = true ? 200 : 404;

       return $response->withStatus($status);

    }


}
