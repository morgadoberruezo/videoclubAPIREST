<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
//para normalizar un objeto
//Para usar los Asertos
use Symfony\Component\Validator\Constraints As Assert;
use Symfony\Component\HttpFoundation\JsonResponse;


class DefaultController extends Controller
{
 
    public function loginAction(Request $request){
        $helpers = $this->get("app.helpers");  
        $jwt_auth = $this->get("app.jwt_auth");
        $data_json = $request->get('data', null);
        if ($data_json != null){
           
            $params = json_decode($data_json);
            $email = (isset($params->email) ? $params->email : null);
            $pass = (isset($params->pass) ? $params->pass : null);
            $getHash = (isset($params->getHash) ? $params->getHash : null);
            //creamos un aserto para validar el email
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "email no es válido";
            
            $valid_email = $this->get("validator")->validate($email,$emailConstraint);
            //cifrar el password
            $pwd = hash('sha256', $pass);
            
            if (count($valid_email) == 0 && $pass != null){
                //en caso de email/pass válidos llamamos al servicio de autenticar
                if ($getHash == null || $getHash == "false")
                    $signup = $jwt_auth->signup($email, $pwd);
                else
                    $signup = $jwt_auth->signup($email, $pwd, true);
                return new JsonResponse($signup);
            }else{
                return $helpers->a_json(array(
                    "status" => "error",
                    "data" => "Login not valid !!"
                ));
            };
        }else{
            return $helpers->a_json(array(
                    "status" => "error",
                    "data" => "Send json with post !!"
            ));
        };
    }
    
    public function probarTokenAction(Request $request){
        $helpers = $this->get("app.helpers");  
        $hash = $request->get ("autorizacion");
        $check = $helpers->authCheck($hash, true);
        var_dump($check);
        die();
    }

    public function indexAction(Request $request)
    {
   
        // replace this example code with whatever you need
        $users = $this->getDoctrine()
                ->getRepository('BDBundle:Users')
                ->getUsers();
        //invocamos al serviceManager
        $helpers = $this->get("app.helpers");     
        
        return $helpers->a_json($users);
    }
        
}
