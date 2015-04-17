<?php

namespace PX500\CoreBundle\Controller;

use PX500\CoreBundle\Entity\Photo;
use PX500\CoreBundle\Entity\PhotoStat;
use PX500\CoreBundle\Entity\User;
use PX500\CoreBundle\Entity\UserStat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @param $name
     * @return Response
     */
    public function indexAction($name)
    {
        return $this->render('PX500CoreBundle:Default:index.html.twig', array('name' => $name));
    }


    /**
     * Update photos
     * @return Response
     */
    public function updateAction()
    {
        $dataService = $this->get('service.data');
        $dataService->updateAll();

        return $this->render('PX500CoreBundle:Default:index.html.twig', array('name' => "world"));
    }

    public function listPhotosAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get users
        $users = $em->getRepository("PX500CoreBundle:User")->findAll();

        return $this->render('PX500CoreBundle:Default:listPhotos.html.twig', array('users' => $users));
    }

    public function photoAction($uid)
    {
        $em = $this->getDoctrine()->getManager();

        // Get photo
        $photo = $em->getRepository("PX500CoreBundle:Photo")->findOneByUid($uid);

        return $this->render('PX500CoreBundle:Default:photo.html.twig', array('photo' => $photo));
    }
}
