<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints As Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use BDBundle\Entity\Users;
class UserController extends Controller
{
 
    public function newAction(Request $request){
        $helpers = $this->get("app.helpers");
        //recogo los datos del registro nuevo que llega por POST
        $nuevo = $request->get("nuevo",null);
        //en $params tendremos los datos de las propiedades del 
        //formulario en json
        $params = json_decode($nuevo);
        //suponemos el usuario nocreado
        $data = array(
                "status"  => "error",
                "code"    => 400,
                "msg"     => "User no creado"
        ); 
        if ($nuevo != null){
            //recogemos los datos del objeto
            $createdAt = new \DateTime("now");
            $image = null;
            $role = "usuario";
            $email = (isset($params->email)) ? $params->email : null;
            $name = (isset($params->name) && ctype_alpha($params->name)) 
                    ? $params->name : null;
            $surname = (isset($params->surname) && ctype_alpha($params->surname)) 
                    ? $params->surname : null;
            $passwd = (isset($params->password)) ? $params->password : null;
            
            //validamos email
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "email no es válido";         
            $valid_email = $this->get("validator")->validate($email,$emailConstraint);
            if ($email != null && count($valid_email) == 0 &&
                $passwd != null && $name != null && $surname != null) {
                
                $usuario = new Users();
                $usuario->setCreatedAt($createdAt);
                $usuario->setName($name);
                $usuario->setSurname($surname);
                $usuario->setImage($image);
                $usuario->setRole($role);
                $usuario->setEmail($email);
                
                //cifrar el password
                $pwd = hash('sha256', $passwd);
                $usuario->setPasswd($pwd);
                
                //el email debe de ser único en la BD
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository("BDBundle:Users")->findBy(
                        array(
                            "email" => $email
                        ));
                //el email no existe y vamos a insertar
                if (count($user) == 0){
                    $em->persist($usuario);
                    $em->flush();
                    $data = array(
                        "status"  => "success",
                        "code"    => 200,
                        "msg"     => "Usario insertado"
                    ); 
                }else {
                    $data = array(
                        "status"  => "error",
                        "code"    => 400,
                        "msg"     => "User no creado, email existe"
                    ); 
                }
            }
        }
        return ($helpers->a_json($data));
    }
    
    public function editAction(Request $request){
        $helpers = $this->get("app.helpers");
        //recogo los datos del registro nuevo que llega por POST
        $nuevo = $request->get("nuevo",null);
        //en $params tendremos los datos de las propiedades del 
        //formulario en json
        $params = json_decode($nuevo);
        //suponemos el usuario nocreado
        $data = array(
                "status"  => "error",
                "code"    => 400,
                "msg"     => "User no creado"
        ); 
        if ($nuevo != null){
            //recogemos los datos del objeto
            $createdAt = new \DateTime("now");
            $image = null;
            $role = "usuario";
            $email = (isset($params->email)) ? $params->email : null;
            $name = (isset($params->name) && ctype_alpha($params->name)) 
                    ? $params->name : null;
            $surname = (isset($params->surname) && ctype_alpha($params->surname)) 
                    ? $params->surname : null;
            $passwd = (isset($params->password)) ? $params->password : null;
            
            //validamos email
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "email no es válido";         
            $valid_email = $this->get("validator")->validate($email,$emailConstraint);
            if ($email != null && count($valid_email) == 0 &&
                $passwd != null && $name != null && $surname != null) {
                
                $usuario = new Users();
                $usuario->setCreatedAt($createdAt);
                $usuario->setName($name);
                $usuario->setSurname($surname);
                $usuario->setImage($image);
                $usuario->setRole($role);
                $usuario->setEmail($email);
                
                //cifrar el password
                $pwd = hash('sha256', $passwd);
                $usuario->setPasswd($pwd);
                
                //el email debe de ser único en la BD
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository("BDBundle:Users")->findBy(
                        array(
                            "email" => $email
                        ));
                //el email no existe y vamos a insertar
                if (count($user) == 0){
                    $em->persist($usuario);
                    $em->flush();
                    $data = array(
                        "status"  => "success",
                        "code"    => 200,
                        "msg"     => "Usario insertado"
                    ); 
                }else {
                    $data = array(
                        "status"  => "error",
                        "code"    => 400,
                        "msg"     => "User no creado, email existe"
                    ); 
                }
            }
        }
        return ($helpers->a_json($data));
    }
    
    public function uploadImageAction(Request $request){
        //comprobamos si el usuario está logeado y nos llega un token correcto
        $helpers = $this->get("app.helpers");
        //comprobamos si el token que llega por post es correcto
        $hash = $request->get("autorization", null);
        //con autCheck() comprobamos el token
        $autCheck = $helpers->authCheck($hash);
        if ($autCheck){ //token válido
            //con el flag true, decodificamos el hash y obtenemos los datos puros
            //del usuario en $data_usu
            $data_usu = $helpers->authCheck($hash, true);
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("BDBundle:Users")->findOneBy(array(
                "id" => $data_usu->sub
            ));
            //recogemos el fichero file por POST
            $file = $request->files->get("image");
            if (!empty($file) && $file != null){
                $extension = $file->guessExtension();
                if ($extension == "jpeg" || $extension == "jpg" || $extension == "png" || $extension == "gif"){ 
                  //formato válido
                    $filename = time().".".$extension;
                    $file->move("uploads/users", $filename);
                    $user->setImage($filename);
                    $em->persist($user);
                    $em->flush();
                    $data = array(
                        "status" => "success",
                        "code"   => 200,
                        "msg"    => "Image for user uploaded success"
                    );
                }else { //imagen de formato nó válido
                    $data = array(
                        "status" => "error",
                        "code"   => 400,
                        "msg"    => "File not valid !!"
                    );
                }
            }else {//fichero llega vacío
                $data = array(
                    "status" => "error",
                    "code"   => 400,
                    "msg"    => "Image not uploaded"
                );
            }
            
        }else { //token no válido
            $data = array(
                "status" => "error",
                "code"   => 400,
                "msg"    => "Autorization not valid"
            );
        }
        return $helpers->a_json($data);
    }
  
}
