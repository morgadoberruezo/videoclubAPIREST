<?php
namespace AppBundle\Services;
use Firebase\JWT\JWT;
class JwtAuth {
    public $emanager; 
    public $key = "clave secreta";
    
    public function __construct($manager) {
        $this->emanager = $manager;
    }
    
    public function checkToken($jwt, $getIdentity = false){
        //por si el token recibido es incorrecto 
        $auth = false; 
        try {
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
            //si es incorreto puede arrojar varios errores excepciones 
            //que debemos capturar
        } catch (\UnexpectedValueException $ex) { //ecepción común
            $auth = false;
        } catch (\DomainException $ex) { //ecepción común
            $auth = false;
        }
        //si existe el sub el token es correcto y ha decodificado los datos
        if (isset($decoded->sub)){ //sub es el id
            $auth = true;
        }else {
            $auth = false;
        }
        
        if ($getIdentity == true){
            return $decoded;
        }else{
            return $auth;
        }
    }

    public function signup($email, $password, $getHash = NULL) {
        
        $user = $this->emanager->getRepository('BDBundle:Users')
                ->findOneBy(array(
                    'email' => $email,
                    'passwd' => $password
                ));
        $signup = false;
        if (is_object($user)){
            $signup = true;
        }
        if ($signup) {
            $key = "clave secreta";
            $token = array(
                "sub" => $user->getId(),
                "email" => $user->getEmail(),
                "name" => $user->getName(),
                "surname" => $user->getSurname(),
                "password" => $user->getPasswd(),
                "image" => $user->getImage(),
                "iat" => time(),
                "exp" => time() + (7 * 24 * 60* 60)
            );    
            $hash_jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($hash_jwt, $this->key, array('HS256'));
            if ($getHash != null)
                return $hash_jwt;
            else
                return $decoded;
           
        }else{
            return array("status" => "error", "data"=>"Login Failed");
        }
    }
}
