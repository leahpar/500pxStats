<?php

namespace PX500\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PX500\CoreBundle\Entity\UserRepository")
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="uid", type="integer", unique=true)
     */
    private $uid;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var ArrayCollection ArrayCollection of Photo
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="user", cascade={"persist"})
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $photos;

    /**
     * @var ArrayCollection ArrayCollection of UserStat
     * @ORM\OneToMany(targetEntity="UserStat", mappedBy="user", cascade={"persist"})
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $stats;

    /**
     * @var integer
     *
     * @ORM\Column(name="photos_count", type="integer")
     */
    private $photosCount;



    /**
     * default constructor
     */
    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->stats = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set uid
     *
     * @param integer $uid
     * @return User
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer 
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set photos
     *
     * @param array $photos
     * @return User
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;

        return $this;
    }

    /**
     * Get photos
     *
     * @return array
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Add a photo
     *
     * @param Photo $photo
     * @return $this
     */
    public function addPhoto(Photo $photo)
    {
        $this->photos->add($photo);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param ArrayCollection $stats
     */
    public function setStats($stats)
    {
        $this->stats = $stats;
    }

    /**
     * Add a stat
     *
     * @param UserStat $userStat
     * @return $this
     */
    public function addStat(UserStat $userStat)
    {
        $this->stats->add($userStat);

        return $this;
    }

    /**
     * Get delay from last update
     *
     * @return \DateInterval
     */
    public function getDelayLastUpdate()
    {
        if (count($this->stats) > 0)
        {
            return floor((time() - $this->stats->last()->getDate()->format('U')) / 60);
        }
        else
        {
            return 86400;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '[User'
            .' uid:'.$this->uid
            .' username:'.$this->username
            .' count:'.$this->photosCount
            .']';
    }

    /**
     * @return int
     */
    public function getPhotosCount()
    {
        return $this->photosCount;
    }

    /**
     * @param int $photosCount
     *
     * @return $this
     */
    public function setPhotosCount($photosCount)
    {
        $this->photosCount = $photosCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

}
