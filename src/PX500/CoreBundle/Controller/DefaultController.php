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
        $em = $this->getDoctrine()->getManager();
        $dataService = $this->get('service.data');

        // get all users
        $users = $em->getRepository("PX500CoreBundle:User")->findAll();

        //echo "<pre>";

        /**
         * @var User $user
         * @var Photo $photo
         * @var UserStat $userStat
         * @var PhotoStat $photoStat
         */

        foreach($users as $user)
        {
            // Update user
            $dataService->log("Update user");
            $photosCount = $user->getPhotosCount();
            $minFromLastUpdate = $user->getDelayLastUpdate();
            $userStat = $dataService->updateUser($user);

            $dataService->log("Last update $minFromLastUpdate min ago");

            // save stat
            if ($minFromLastUpdate > 10)
            {
                $dataService->log("persist stat");
                $em->persist($userStat);
            }

            // Get new photo
            $dataService->log("Check new photo");
            if ($user->getPhotosCount() > $photosCount)
            {
                $dataService->log("Get new photo");
                $photo = $dataService->getPhoto($user);
                $user->addPhoto($photo);
                $em->persist($photo);
            }

            // Update photos
            $dataService->log("Update photos");
            $photos = $user->getPhotos();
            foreach ($photos as $photo)
            {
                $minFromUpload = $photo->getDelay();
                $minFromLastUpdate = $photo->getDelayLastUpdate();
                $dataService->log("Last update $minFromLastUpdate min ago");
                $delay = 0;

                if ($minFromUpload < 30)
                {
                    $delay = 1;
                }
                else if ($minFromUpload < 2000)
                {
                    $delay = 5;
                }
                else
                {
                    $delay = -1;
                }

                // time to update
                if ($delay > 0 && $minFromLastUpdate > $delay)
                {
                    $dataService->log("Time to update");
                    $photoStat = $dataService->getPhotoStats($photo);
                    $photo->addStat($photoStat);
                    $em->persist($photoStat);
                }
                else
                {
                    $dataService->log("No update");
                }
            }
        }
        $dataService->log("The end");
        $em->flush();

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
