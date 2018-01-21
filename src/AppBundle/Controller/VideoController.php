<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints As Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use BDBundle\Entity\Users;
use BDBundle\Entity\Videos;

class VideoController extends Controller
{
 
    public function newAction(Request $request){
        $helpers = $this->get("app.helpers");
        $hash = $request->get("autorization", null);
        $autCheck = $helpers->authCheck($hash);
        if ($autCheck == true){
            //obtenemos los datos límpios del token con el flag true
            $usuario = $helpers->authCheck($hash, true);
            $datos_json = $request->get('datos', null);
            if ($datos_json != null){
                $params = json_decode($datos_json);
                
                $createdAt = new \DateTime('now');
                $updatedAt = new \DateTime('now');
                $image = null;
                $video_path = null;
                $user_id = ($usuario->sub != null) ? $usuario->sub : null;
                $titulo = (isset($params->titulo)) ? $params->titulo : null;
                $descripcion = (isset($params->descripcion)) ? $params->descripcion : null;
                $status = (isset($params->status)) ? $params->status : null;
                if ($user_id && $titulo){
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository("BDBundle:Users")->findOneBy(
                            array(
                                "id" => $user_id,
                            ));
                    $video = new Videos();
                    $video->setUser($user);
                    $video->setTitle($titulo);
                    $video->setDescripcion($descripcion);
                    $video->setStatus($status);
                    $video->setCreatedAt($createdAt);
                    $video->setUpdatedAt($updatedAt);
                    $em->persist($video);
                    $em->flush();
                    //hacemos que devuelva los datos del video
                    $video = $em->getRepository("BDBundle:Videos")->findOneBy(
                            array(
                                "user"      => $user,
                                "title"     => $titulo,
                                "status"    => $status,
                                "createdAt" => $createdAt
                            ));
                    $data = array(
                        "status" =>"success",
                        "code" => 200,
                        "data" => $video
                    );
                }else {
                    $data = array(
                        "status" =>"error",
                        "code" => 400,
                        "msg" => "Video not created"
                    );
                }
            }else {
                $data = array(
                        "status" =>"error",
                        "code" => 400,
                        "msg" => "Video not created, params failed"
                    );
            }
        }else {
            $data = array(
                "status" =>"error",
                "code" => 400,
                "msg" => "Authorization not valid"
            );
        }
        return ($helpers->a_json($data));
    }
    
    
  
}
