<?php

namespace PX500\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Photo
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PX500\CoreBundle\Entity\PhotoRepository")
 */
class Photo
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="PX500\CoreBundle\Entity\User", inversedBy="photos")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="uid", type="integer")
     */
    private $uid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var ArrayCollection ArrayCollection of PhotoStat
     * @ORM\OneToMany(targetEntity="PhotoStat", mappedBy="photo", cascade={"persist"})
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $stats;

    /**
     * @var \DateInterval
     */
    private $delay;

    /**
     * default constructor
     */
    public function __construct()
    {
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
     * @return Photo
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
     * Set date
     *
     * @param \DateTime $date
     * @return Photo
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Photo
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get delay from upload
     *
     * @return \DateInterval
     */
    public function getDelay()
    {
        if (empty($this->delay))
        {
            $this->delay = (new \DateTime())->diff($this->getDate());
        }
        return $this->delay;
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
            return (new \DateTime())->diff($this->stats->last()->getDate());
        }
        else
        {
            return (new \DateInterval('PT1D'));
        }
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
     * @param PhotoStat $photoStat
     * @return $this
     */
    public function addStat(PhotoStat $photoStat)
    {
        $this->stats->add($photoStat);

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

}
