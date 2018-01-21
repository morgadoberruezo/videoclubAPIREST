<?php
namespace AppBundle\Services;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
//para transformar el objeto normalizado a un objeto json
use Symfony\Component\Serializer\Encoder\JsonEncoder;
//con los normalizers y encoders serializamos el objeto
use Symfony\Component\Serializer\Serializer;
//usamos el objeto Response para devolver la respuesta
use Symfony\Component\HttpFoundation\Response;

class Helpers {
    public $jwt_auth;
    
    public function __construct($jwt_auth) {
        $this->jwt_auth = $jwt_auth;
    }
    
    public function authCheck($hash, $getIdentity = false) {
        $jwt_auth = $this->jwt_auth;
        $auth = false;
        if ($hash != null){
            if ($getIdentity == false){
                //si el token es válido devuelvo true
                $check_token = $jwt_auth->checkToken($hash);
                if ($check_token == true)
                    $auth = true;
            }else {//$getIdentity == false
                //si el token es válido devuelvo el objeto decodificado
                $check_token = $jwt_auth->checkToken($hash, true);
                if (is_object($check_token)){
                    $auth = $check_token;
                }
            }
        }
        return $auth;
        
    }

    public function a_json ($dato) {
        $normalizar = array(new GetSetMethodNormalizer());
        $encoders = array("json" => new JsonEncoder());
        $serializer = new Serializer($normalizar, $encoders);
        $datos_json = $serializer->serialize($dato, 'json');
        //creamos una respuesta con los datos y las cabeceras http puestas
        $response = new Response();
        $response->setContent($datos_json);
        $response->headers->set("Content-Type","application/json");    
        return $response;
    }
}
