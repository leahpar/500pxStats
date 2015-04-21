<?php

namespace PX500\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\HttpException;

use PX500\CoreBundle\Entity\Photo;
use PX500\CoreBundle\Entity\PhotoStat;
use PX500\CoreBundle\Entity\User;
use PX500\CoreBundle\Entity\UserStat;


class DataService
{
    protected $em;
    public $api_url;
    public $api_key;

    /**
     * Constructor
     * @param EntityManager $em
     * @param String $api_url
     * @param String $api_key
     */
    public function __construct(EntityManager $em, $api_url, $api_key)
    {
        $this->em = $em;
        $this->api_url = $api_url;
        $this->api_key = $api_key;
    }

    public function updateAll()
    {
        $em = $this->em;

        // get all users
        $users = $em->getRepository("PX500CoreBundle:User")->findAll();

        /**
         * @var User $user
         * @var Photo $photo
         * @var UserStat $userStat
         * @var PhotoStat $photoStat
         */

        foreach($users as $user)
        {
            try {
                // Update user
                $this->log("Update user");
                $photosCount = $user->getPhotosCount();
                $minFromLastUpdate = $user->getDelayLastUpdate();
                $userStat = $this->updateUser($user);

                $this->log("Last update $minFromLastUpdate min ago");

                // save stat
                if ($minFromLastUpdate > 10) {
                    $this->log("persist stat");
                    $em->persist($userStat);
                }

                // Get new photo(s)
                $this->log("Check new photo");
                for ($i = $user->getPhotosCount() - $photosCount; $i > 0; $i--) {
                    $this->log("Get new photo ($i)");
                    $photo = $this->getPhoto($user, $i-1);
                    $user->addPhoto($photo);
                    $em->persist($photo);
                }

                // Update photos
                $this->log("Update photos");
                $photos = $user->getPhotos();
                foreach ($photos as $photo) {
                    try {
                        $minFromUpload = $photo->getDelay();
                        $minFromLastUpdate = $photo->getDelayLastUpdate();
                        $this->log("Last update $minFromLastUpdate min ago, uploaded $minFromUpload min ago");
                        $delay = 0;

                        if ($minFromUpload < 30) {
                            $delay = 1;
                        } else if ($minFromUpload < 2000) {
                            $delay = 5;
                        } else {
                            $delay = -1;
                        }

                        // time to update
                        if ($delay > 0 && $minFromLastUpdate > $delay) {
                            $this->log("Time to update");
                            $photoStat = $this->getPhotoStats($photo);
                            $photo->addStat($photoStat);
                            $em->persist($photoStat);
                        } else {
                            $this->log("No update");
                        }
                    } catch (HttpException $e) {
                        // continue next photo
                    }
                }
            } catch (HttpException $e) {
                // continue next user
            }
        }
        $this->log("The end");
        $em->flush();
    }


    /**
     * Call 500px api to update a user
     * @param User $user
     * @return new UserStat, false if an error occurs
     */
    public function updateUser(User $user)
    {
        $this->log("=> updateUser($user)");
        // set api url
        $url  = $this->api_url;
        $url .= '/users/show';
        $url .= '?id='.$user->getUid();
        $url .= '&consumer_key='.$this->api_key;

        // call 500px api
        $data = $this->getDataFromUrl($url); // throws HttpException
        $userData = $data['user'];

        // update user
        $user->setPhotosCount($userData['photos_count']);

        // Create new user stat
        $userStat = new UserStat();
        $userStat->setDate(new \DateTime());
        $userStat->setAffection($userData['affection']);
        $userStat->setFollowers($userData['followers_count']);
        $userStat->setUser($user);

        $this->log("<= updateUser() = $userStat");
        return $userStat;
    }

    /**
     * Call 500px api to get last nth user's photo
     * @param User $user
     * @param int $nth : last nth photo to get
     * @return new Photo, false if an error occurs
     */
    public function getPhoto(User $user, $nth = 0)
    {
        $this->log("=> getPhoto($user)");

        // set api url
        $url  = $this->api_url;
        $url .= '/photos';
        $url .= '?feature=user';
        $url .= '&user_id='.$user->getUid();
        $url .= '&image_size=3';
        $url .= '&consumer_key='.$this->api_key;

        // call 500px api
        $data = $this->getDataFromUrl($url); // throws HttpException
        $photoData = $data['photos'][$nth];

        // Create new photo
        $photo = new Photo();
        $photo->setUid($photoData['id']);
        $photo->setName($photoData['name']);
        $photo->setUrl($photoData['image_url']);
        $date = date("Y-m-d H:i:s", strtotime($photoData['created_at'])); // convert to local time
        $photo->setDate(\DateTime::createFromFormat("Y-m-d H:i:s", $date));
        $photo->setUser($user);

        $this->log("<= getPhoto() = $photo");
        return $photo;
    }

    /**
     * Call 500px api to get last stats of a photo
     * @param Photo $photo
     * @return new PhotoStat, false if an error occurs
     */
    public function getPhotoStats(Photo $photo)
    {
        $this->log("=> getPhotoStats($photo)");
        // set api url
        $url  = $this->api_url;
        $url .= '/photos';
        $url .= '/'.$photo->getUid();
        $url .= '?consumer_key='.$this->api_key;

        // call 500px api
        $data = $this->getDataFromUrl($url); // throws HttpException
        $photoData = $data['photo'];

        // Create new photo stat
        $photoStat = new PhotoStat();
        $photoStat->setDate(new \DateTime());
        $photoStat->setComs($photoData['comments_count']);
        $photoStat->setViews($photoData['times_viewed']);
        $photoStat->setFavs($photoData['favorites_count']);
        $photoStat->setRating($photoData['rating']);
        $photoStat->setLikes($photoData['votes_count']);
        $photoStat->setPhoto($photo);

        $this->log("<= getPhotoStats() = $photoStat");
        return $photoStat;
    }

    /**
     * @param $str
     */
    public function log($str)
    {
        echo (new \DateTime())->format('[H:i:s]').' '.$str."\n";
    }


    /**
     * @param $url
     * @return array
     */
    public function getDataFromUrl($url)
    {
        // camll 500px api
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // get json
        $json = curl_exec($curl);
        $response = curl_getinfo($curl);
        curl_close($curl);

        //$this->log("$url");
        $this->log("Result API : ".$response['http_code']);
        if ($json === null)
        {
            throw new HttpException(0, "invalid json");
        }
        elseif ($response['http_code'] != 200)
        {
            throw new HttpException($response['http_code'], "500px api url = [$url]");
        }

        // decode json
        $data = json_decode($json, true);

        return $data;
    }

    /**
     * Clean data in DB : add missing attributes
     * @param $cleanUsers : clean User table ?
     * @param $cleanPhotos : clean Photo table ?
     */
    public function cleandb($cleanUsers, $cleanPhotos)
    {
        $em = $this->em;

        if ($cleanUsers) {
            // get all users
            $users = $em->getRepository("PX500CoreBundle:User")->findAll();
            /** @var User $user */
            foreach ($users as $user) {

                // If some user's attribute is empty, update it
                $update = false;
                if (empty($user->getUsername())
                    || empty($user->getName())
                    || empty($user->getPhotosCount())) {

                    try {
                        // set api url
                        $url = $this->api_url;
                        $url .= '/users/show';
                        $url .= '?id=' . $user->getUid();
                        $url .= '&consumer_key=' . $this->api_key;

                        // call 500px api
                        $data = $this->getDataFromUrl($url); // throws HttpException
                        $userData = $data['user'];

                        // update user
                        $user->setUsername($userData['username']);
                        $user->setPhotosCount($userData['photos_count']);
                        $user->setName($userData['firstname'] . ' ' . $userData['lastname']);
                    } catch (HttpException $e) {
                        // TODO : handle exception
                    }
                }
            }
            $em->flush();
        }

        if ($cleanPhotos) {
            // get all photos
            $photos = $em->getRepository("PX500CoreBundle:Photo")->findAll();
            /** @var Photo $photo */
            foreach ($photos as $photo) {

                // If some photo's attribute is emtpy, update it
                if (empty($photo->getName()) || empty($photo->getUrl())) {

                    try {
                        // set api url
                        $url = $this->api_url;
                        $url .= '/photos';
                        $url .= '/' . $photo->getUid();
                        $url .= '?image_size=3';
                        $url .= '&consumer_key=' . $this->api_key;

                        // call 500px api
                        $data = $this->getDataFromUrl($url); // throws HttpException
                        $photoData = $data['photo'];

                        // update photo
                        $photo->setName($photoData['name']);
                        $photo->setUrl($photoData['image_url']);
                    } catch (HttpException $e) {
                        // TODO : handle exception
                    }
                }
            }
            $em->flush();
        }
    }
}
