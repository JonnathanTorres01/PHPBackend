<?php

//conntroladores

namespace App\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

class Cliente extends AccesoBD{

    const RECURSO = "Cliente";

    public function crear(Request $request, Response $response, $args){
        //(:idCliente, :serie, :modelo, :marca, :categoria, :descripcion)
        
       $body = json_decode($request -> getBody());
        //Hash pasword
       //$body -> passw = $body->passw; //esto hay que revisarlo
/*
       $body -> id = $body->id;
       $body -> idCliente = $body->idCliente;
       $body -> nombre = $body->nombre;
       $body -> apellido1 = $body->apellido1;
       $body -> apellido2 = $body->apellido2;
       $body -> telefono = $body->telefono;
       $body -> celular = $body->celular;
       $body -> direccion = $body->direccion;
       $body -> correo = $body->correo;
       $body -> fechaIngreso = $body->fechaIngreso;
*/
       /*
       $d["id"] = $valor;
       $d["idCliente"] = $valor;
       $d["nombre"] = $valor;
       $d["apellido1"] = $valor;
       $d["apellido2"] = $valor;
       $d["telefono"] = $valor;
       $d["celular"] = $valor;
       $d["direccion"] = $valor;
       $d["correo"] = $valor;
       $d["fechaIngreso"] = $valor;
       */

        password_hash($body -> idCliente, PASSWORD_BCRYPT, ['cost' => 10]);
        
       // $rol = $body->rol;
       // $passw = $body->passw;


       // var_dump($body);
        //var_dump($rol);
        //var_dump($passw);

        //die();


        $res = $this->crearUsrBD($body, self::RECURSO);
         $status = match($res){
            '0',0 => 201,
            '1',1 => 409,
            '2',2 => 500,
        };
        return $response->withStatus($status);
    }

    

    public function editar(Request $request, Response $response, $args){
        //(:id, :serie, :modelo, :marca, :categoria, :descripcion)
        $id = $args['id'];
        $body = json_decode($request -> getBody(), 1);
        $res = $this->editarBD($body, self::RECURSO, $id);        

        //un switch 
        $status = match($res[0]){
            '0',0 => 404,//not found
            '1',1 => 200,
            '2',2 => 409
        };

        return $response->withStatus($status);
    }

    public function cambiarPropietario(Request $request, Response $response, $args){
        //(:id, :idCliente)
        $body = json_decode($request -> getBody(), 1);
        $res = $this->cambiarPropietarioBD(
            ['id' => $args['id'], 
            'idCliente' => $body['idCliente']]);

        $status = match($res){
            '0' => 404,//not found
            '1' => 200,
            '2' => 409
        };
        return $response->withStatus($status);
    }

    public function eliminar(Request $request, Response $response, $args){
        
        $res = $this ->eliminarBD($args['id'], self::RECURSO);
        $status = $res > 0 ? 200 : 404;
        /*
        $status = match($res){
            '0', 0 => 
        }
        */
        return $response->withStatus($status);

    }
    
    public function buscar(Request $request, Response $response, $args){
        $id = $args['id'];

        $res = $this->buscarBD($id, self::RECURSO);
        $status = !$res ? 404 : 200;

       // $status = $query->rowCount() > 0 ? 200 : 404;
       // if($res){
            $response->getBody()->write(json_encode($res));
        //}
        return $response
            ->withHeader('Content-type', 'Application/json')
            ->withStatus($status);
    }


    public function buscarUno(Request $request, Response $response, $args){
        $id = $args['id'];

        $res = $this->buscarClienteUnoBD($id, self::RECURSO);
        $status = !$res ? 404 : 200;

       // $status = $query->rowCount() > 0 ? 200 : 404;
       // if($res){
            $response->getBody()->write(json_encode($res));
        //}
        return $response
            ->withHeader('Content-type', 'Application/json')
            ->withStatus($status);
    }
    

    public function filtrar(Request $request, Response $response, $args){
        $datos = $request->getQueryParams();
        $res = $this -> filtrarBD($datos, $args, self::RECURSO);
        
        $status = sizeof($res) > 0 ? 200: 204;
        $response->getBody()->write(json_encode($res));        
        return $response
            ->withHeader('Content-type', 'Application/json')
            ->withStatus($status);
    }

    public function numRegs(Request $request, Response $response, $args){
        $datos = $request->getQueryParams();
        $res['cant'] = $this->numRegsBD($datos, self::RECURSO);

        $response->getBody()->write(json_encode($res));
        return $response
            ->withHeader('Content-type', 'Application/json')
            ->withStatus(200);
    }


}