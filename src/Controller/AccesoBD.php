<?php
//conntroladores

namespace App\controller;
use Psr\Container\ContainerInterface;
use PDO;

class AccesoBD{
    //atributos
    protected $container;
    //constructor en php
    public function __construct(ContainerInterface $c){
        $this->container = $c;
    }

    private function generarParam($datos){
        $cad = "(";
        foreach($datos as $campo => $valor){
            $cad .= ":$campo,";
        }
        $cad =trim($cad, ',');
        $cad .=")";

        return $cad;
    }

    public function crearBD($datos, $recurso){
        $params = $this->generarParam($datos);
        $sql = "SELECT nuevo$recurso$params";
        $d = [];
        foreach($datos as $clave =>$valor){
            $d[$clave] = $valor;
        }
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute($d);
        $res = $query->fetch(PDO::FETCH_NUM);
        $query = null;
        $con = null;
        return $res[0];
    }


    public function crearUsrBD($datos, $recurso){
        //$passw = $datos->passw;
        //unset()
        $params = $this->generarParam($datos);
        
        $con = $this->container->get('bd');
        $con -> beginTransaction();
        try{
            $sql = "SELECT nuevo$recurso$params";
            $query = $con->prepare($sql);
            $d = [];
           // $valor = 10;

           /*
           for ($i = 0; $i < 10; $i++) {

            print "<p>$matriz[$i]</p>\n";
        
        }
*/

            foreach($datos as $clave=>$valor){
                $d[$clave] = $valor;
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
        }

            

            $query->execute($d);
            $res = $query->fetch(PDO::FETCH_NUM)[0];
/*
            //crear el usuario
            $sql = "SELECT nuevoUsuario(:idUsuario, :rol, :passw);";
            $query = $con -> prepare($sql);
            $query ->execute(array(
                'idUsuario' => $d[campoID],
                'rol' => $rol,
                'passw' =>$passw
            ));
            */
            $con ->commit();

        }
        catch(PDDExeption $ex){


            $con -> rollback();
            $res =2;

        }       
        $query = null;
        $con = null;
        return $res[0];
    }






    public function editarBD($datos,$recurso, $id){
        $params = $this->generarParam($datos);
        $params = substr($params, 0, 1) . ":id," . substr($params, 1);
        $sql = "SELECT editar$recurso$params";
        $d['id'] = $id;
        foreach($datos as $clave =>$valor){
            $d[$clave] = $valor;
        }
        
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute($d);
        $res = $query->fetch(PDO::FETCH_NUM);
        $query = null;
        $con = null;
        return $res[0];
    }

    public function eliminarBD($id, $recurso){

        $sql = "SELECT eliminar$recurso(:id);";

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);

        $query->execute(["id" =>$id]);

        $res = $query->fetch(PDO::FETCH_NUM)[0];
        $query = null;
        $con = null;

        return $res;
    }


    public function buscarBD($id, $recurso){
        $sql = "CALL buscar$recurso(:id);";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute(['id' => $id]);
        $res = $query->fetch(PDO::FETCH_ASSOC);
        //var_dump($res); die();
        $query = null;
        $con = null;        
        return $res;
    }

    
    public function buscarClienteUnoBD($id, $recurso){
        $sql = "CALL buscarUno$recurso(:id);";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute(['id' => $id]);
        $res = $query->fetch(PDO::FETCH_ASSOC);
        //var_dump($res); die();
        $query = null;
        $con = null;        
        return $res;
    }


    public function filtrarBD($datos, $args, $recurso){
    
        $limite = $args['limite'];
        $pagina = ($args['pagina'] - 1) * $limite;
        $cadena = "";
        foreach($datos as $valor){
            $cadena .= "%$valor%&";
        }

        $sql = "call filtrar$recurso('$cadena', $pagina, $limite);";

        //var_dump($sql);

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute();


        $res = $query->fetchAll();

        //$numRegs = $this->numRegsBD($datos,$recurso);
        //die();

        $query = null;
        $con = null;

        $datosRetorno['datos'] = $res;
        $datosRetorno['regs'] = $this->numRegsBD($datos,$recurso);

//        var_dump($res);
        
        return $datosRetorno;

    }

    public function numRegsBD($datos, $recurso){
    
        $cadena = "";
        foreach($datos as $valor){
            $cadena .= "%$valor%&";
        }
        $sql = "call numRegs$recurso('$cadena');";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->execute();
        $res = $query->fetch(PDO::FETCH_NUM)[0];
        $query = null;
        $con = null;
        return $res;
    
    }


    public function cambiarPropietarioBD($d){
        $params = $this->generarParam($d);
        $sql = "SELECT cambiarPropietario$params";
        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        $query->bindParam(':id', $d['id'], PDO::PARAM_INT);
        $query->bindParam(':idCliente', $d['idCliente'], PDO::PARAM_INT);
        $query->execute();
        $res = $query->fetch(PDO::FETCH_NUM)[0];
        $query = null;
        $con = null;
        return $res;
    }


public function editarUsuario(string $idUsuario, int $rol = -1, string $passw = ''){

$proc = $rol == -1 ? ' select passwUsuario(:id, :passw);' : "select rolUsuario(:id, :rol);";
$sql = "call buscarUsuario(0,$idUsuario);";

$con = $this->container->get('bd');
$query = $con->prepare($sql);

$query->execute();

$usuario = $query->fetch(PDO::FETCH_ASSOC);

if($usuario){

$params = ['id' => $usuario['id']];
$params = $rol == -1 ? array_merge($params, ['passw' => $passw]) :
                    array_merge($params, ['rol' => $rol]);

                    $query = $con->prepare($proc);
                    $retorno = $query->execute($params);

}else{
    $retorno = false;
}


$query = null;
$con = null;

return $retorno;

//var_dump($params); die();

}


public function buscarUsr(int $id =0, string $idUsuario = ''){
   // $sql = "CALL buscarUsuario($id, $idUsuario);";
    $con = $this->container->get('bd');
    $query = $con->prepare("CALL buscarUsuario($id, $idUsuario);");
    
    //$query->execute(['id' => $id]);
    
    $query->execute();
    
   // $res = $query->fetch(PDO::FETCH_ASSOC);

   $res = $query->fetch();

    //var_dump($res); die();
    $query = null;
    $con = null;        
//+++++    
return $res;
}

public function buscarNombre($id, string $tipoUsuario){
 
    $proc = 'buscar' . $tipoUsuario . "(0, '$id')";

    $sql = "CALL $proc";

    $con = $this->container->get('bd');
     
    //$query = $con->prepare("CALL buscarUsuario($id, $idUsuario);");
     
     $query = $con->prepare($sql);

     $query->execute();
 
     if($query->rowCount() > 0){

     $res = $query->fetch(PDO::FETCH_ASSOC);

     }else{
        $res = [];
     }

     //var_dump($res);
     //die();

 
     $query = null;
     $con = null;
     
     $res = $res['nombre'];

     if(str_contains($res, "")){
        $res = substr($res, 0,strpos($res, " "));
     }
     
     return $res;
 }


public function accederToken(string $proc, string $idUsuario, string $tokenRef=""){

$sql = $proc == "modificar" ? "select modificarToken(:idUsuario, :tk);" :
                               "call verificarToken(:idUsuario, :tk);";
                               $con = $this->container->get('bd');
                               $query = $con->prepare($sql);
                               $query->execute(["idUsuario"=>$idUsuario, "tk"=>$tokenRef]);
if($proc == "modificar"){
$datos = $query->fetch(PDO::FETCH_NUM);

}else{
    $datos = $query->fetchColumn();
}

$query = null;
$con = null;
return $datos;


}







}