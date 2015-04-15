<?php

namespace PX500\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PX500CoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
