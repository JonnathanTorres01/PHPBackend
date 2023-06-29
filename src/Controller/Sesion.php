<?php

namespace App\controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;

use Firebase\JWT\JWT;

class Sesion extends AccesoBD{

    const TIPO_USR = [
        1 => "Admin",
        2 => "Oficinista",
        3 => "Tecnico",
        4 => "Cliente"
    ];

    private function modificarToken(string $idUsuario, string $tokenRef= ""){

        return $this->accederToken('modificar',$idUsuario, $tokenRef);
        
        }

        private function verificarRefresco(string $idUsuario, string $tokenRef){

            return $this->accederToken('verificar',$idUsuario, $tokenRef);
            
            }

        
        public function generarTokens(string $idUsuario, int $rol, string $nombre){
        
       // $key = 'Alguna Clave'; //crear una clave
        
       $key = $this->container->get('clave');

        $payload = [
        
        'iss' => $_SERVER['SERVER_NAME'],
        'iat' => time(),
        'exp' => time() + 60,
        'sub' =>  $idUsuario,
        'rol' => $rol,
        'nom' => $nombre
        
        ];

        


        $payloadRef = [
        
            'iss' => $_SERVER['SERVER_NAME'],
            'iat' => time(),
            'rol' => $rol
            ];
        
        
            $tkRef = JWT::encode($payloadRef,$key, 'HS256');
            //$tk = JWT::encode($payloadRef,$key, 'HS256');
            //Guardar el token
        //$acceso =    
        
        $this->modificarToken(idUsuario: $idUsuario, tokenRef: $tkRef);
        
          //  die($tkRef);
        
        
        return [
            "token" => JWT::encode($payload, $key, 'HS256'),
            "refreshToken" => $tkRef
        ];
        
        
        }




    private function autenticar($idUsuario, $passw){

        $datos = $this->buscarUsr(idUsuario: $idUsuario);

        return(($datos) && (password_verify($passw,$datos->passw))) ?
        ['rol' => $datos->rol] : null;

        //var_dump($datos); die();
    }


public function iniciar(Request $request, Response $response, $args){

    $body = json_decode($request->getbody());
     
    $res = $this->autenticar($args['id'], $body->passw);

    //var_dump($res); die();

    if($res){
        
        $nombre = $this->buscarNombre($args['id'], self::TIPO_USR[$res['rol']]);

//        $nombre = $this->buscarNombre($args['id'], self::TIPO_USR[$res]);

        $tokens = $this->generarTokens($args['id'], $res['rol'], $nombre);

        $response->getBody()->write(json_encode($tokens));

        $status = 200;

    }else {
        $status = 401;
    }

    return $response
            ->withHeader('Content-type', 'Application/json')
            ->withStatus($status);
}

public function cerrar(Request $request, Response $response, $args){

$this->modificarToken(idUsuario: $args['id']);
return $response->withStatus(200);



}

public function refrescar(Request $request, Response $response, $args){

 $body = json_decode($request->getBody());
 $rol = $this->verificarRefresco($args['id'], $body->tkR);

//var_dump($rol); die();

 if($rol){
 $nombre = $this->buscarNombre($args['id'], self::TIPO_USR[$rol]);
 $tokens = $this->generarTokens($args['id'],$rol, $nombre);
}
if(isset($tokens)){

$status = 200;
$response->getBody()->write(json_encode($tokens));

}
else{
    $status = 401;
}

return $response
->withHeader('Content-type', 'Application/json')
->withStatus($status);


 var_dump($rol);
 die($rol);

}


}