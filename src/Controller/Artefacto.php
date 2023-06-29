<?php

//conntroladores

namespace App\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

class Artefacto extends AccesoBD{

    const RECURSO = "Artefacto";

    public function crear(Request $request, Response $response, $args){
        //(:idCliente, :serie, :modelo, :marca, :categoria, :descripcion)
        $body = json_decode($request -> getBody());
        $res = $this->crearBD($body, self::RECURSO);

        $status = match($res){
            '0' => 201,
            '1' => 409,
            '2' => 404
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
            '0' => 404,//not found
            '1' => 200,
            '2' => 409
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
        return $response->withStatus($status);
    }
    
    public function buscar(Request $request, Response $response, $args){
        $id = $args['id'];

        $res = $this->buscarBD($id, self::RECURSO);
        $status = !$res ? 404 : 200;
        if($res){
            $response->getBody()->write(json_encode($res));
        }
        return $response
            ->withHeader('Content-type', 'Application/json')
            ->withStatus($status);
    }

    public function filtrar(Request $request, Response $response, $args){
        $datos = $request->getQueryParams();

        var_dump($request);

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
