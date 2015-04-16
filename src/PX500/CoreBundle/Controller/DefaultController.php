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
            $photosCount = $user->getPhotosCount();
            $minFromLastUpdate = $user->getDelayLastUpdate()->format('%i');
            var_dump($user->getDelayLastUpdate());
            $userStat = $dataService->updateUser($user);
            // save stat
            if ($minFromLastUpdate > 10)
            {
                $em->persist($userStat);
            }

            // Get new photo
            if ($user->getPhotosCount() > $photosCount)
            {
                $photo = $dataService->getPhoto($user);
                $user->addPhoto($photo);
                $em->persist($photo);
            }

            // Update photos
            $photos = $user->getPhotos();
            foreach ($photos as $photo)
            {
                $minFromUpload = $photo->getDelay()->format('%i');
                $minFromLastUpdate = $photo->getDelayLastUpdate()->format('%i');
                $delay = 0;

                if ($minFromUpload < 10)
                {
                    $delay = 1;
                }
                else
                {
                    $delay = 5;
                }

                // time to update
                if ($minFromLastUpdate > $delay)
                {
                    $photoStat = $dataService->getPhotoStats($photo);
                    $photo->addStat($photoStat);
                    $em->persist($photoStat);
                }
            }
        }
        $em->flush();

        return $this->render('PX500CoreBundle:Default:index.html.twig', array('name' => "world"));
    }
}
