<?php

namespace PX500\CoreBundle\Services;

use PX500\CoreBundle\Entity\Photo;
use PX500\CoreBundle\Entity\PhotoStat;
use PX500\CoreBundle\Entity\User;
use PX500\CoreBundle\Entity\UserStat;

class DataService
{
    protected $api_url;
    protected $api_key;

    /**
     * Constructor
     * @param String $api_url
     * @param String $api_key
     */
    public function __construct($api_url, $api_key)
    {
        $this->api_url = $api_url;
        $this->api_key = $api_key;
    }


    /**
     * Call 500px api to update a user
     * @param User $user
     * @return new UserStat, false if an error occurs
     */
    public function updateUser(User $user)
    {
        // set api url
        $url  = $this->api_url;
        $url .= '/users/show';
        $url .= '?id='.$user->getUid();
        $url .= '&consumer_key='.$this->api_key;

        // call 500px api
        $json = file_get_contents($url);
        if ($json === null) return false;

        // decode json
        $data = json_decode($json);
        if ($data === null) return false;
        $userData = $data['user'];

        // update user
        $user->setPhotos($userData['photos_count']);

        // Create new user stat
        $userStat = new UserStat();
        $userStat->setDate(new \DateTime());
        $userStat->setAffection($userData['affection']);
        $userStat->setFollowers($userData['followers_count']);
        $userStat->setUser($user);

        return $userStat;
    }

    /**
     * Call 500px api to get last user's photo
     * @param User $user
     * @return new Photo, false if an error occurs
     */
    public function getPhoto(User $user)
    {
        // set api url
        $url  = $this->api_url;
        $url .= '/photos';
        $url .= '?feature=user';
        $url .= '&user_id='.$user->getUid();
        $url .= '&consumer_key='.$this->api_key;

        // call 500px api
        $json = file_get_contents($url);
        if ($json === null) return false;

        // decode json
        $data = json_decode($json);
        if ($data === null) return false;
        $photoData = $data['photos'][0];

        // get last photo
        $user->setPhotos($data['photos_count']);

        // Create new photo
        $photo = new Photo();
        $photo->setUid($photoData['id']);
        $photo->setUrl($photoData['image_url']);
        $photo->setDate(\DateTime::createFromFormat(\DateTime::ATOM, $photoData['created_at']));
        $photo->setUser($user);

        return $photo;
    }

    /**
     * Call 500px api to get last stats of a photo
     * @param Photo $photo
     * @return new PhotoStat, false if an error occurs
     */
    public function getPhotoStats(Photo $photo)
    {
        // set api url
        $url  = $this->api_url;
        $url .= '/photos';
        $url .= '?'.$photo->getUid();
        $url .= '&consumer_key='.$this->api_key;

        // call 500px api
        $json = file_get_contents($url);
        if ($json === null) return false;

        // decode json
        $data = json_decode($json);
        if ($data === null) return false;
        $photoData = $data['photo'];

        // Create new photo stat
        $photoStat = new PhotoStat();
        $photoStat->setDate(new \DateTime());
        $photoStat->setComs($photoData['comments_count']);
        $photoStat->setViews($photoData['time_viewed']);
        $photoStat->setFavs($photoData['favorites_count']);
        $photoStat->setRating($photoData['rating']);
        $photoStat->setLikes($photoData['votes_count']);
        $photoStat->setPhoto($photo);

        return $photoStat;
    }
}

