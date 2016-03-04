<?php

namespace PX500\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PhotoStat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PX500\CoreBundle\Entity\PhotoStatRepository")
 */
class PhotoStat
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
     * @var Photo
     *
     * @ORM\ManyToOne(targetEntity="PX500\CoreBundle\Entity\Photo", inversedBy="stats")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $photo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;

    /**
     * @var integer
     *
     * @ORM\Column(name="likes", type="integer")
     */
    private $likes;

    /**
     * @var integer
     *
     * @ORM\Column(name="coms", type="integer")
     */
    private $coms;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="float")
     */
    private $rating;

    /**
     * @var \DateInterval
     */
    private $delay;


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
     * Set date
     *
     * @param \DateTime $date
     * @return PhotoStat
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
     * Set views
     *
     * @param integer $views
     * @return PhotoStat
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set likes
     *
     * @param integer $likes
     * @return PhotoStat
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return integer 
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set coms
     *
     * @param integer $coms
     * @return PhotoStat
     */
    public function setComs($coms)
    {
        $this->coms = $coms;

        return $this;
    }

    /**
     * Get coms
     *
     * @return integer 
     */
    public function getComs()
    {
        return $this->coms;
    }



    /**
     * Get delay
     *
     * @return \DateInterval
     */
    public function getDelay()
    {
        if (empty($this->delay))
        {
            $this->delay = floor(($this->getDate()->format('U') - $this->getPhoto()->getDate()->format('U')) / 60);
        }
        return $this->delay;
    }

    /**
     * @return Photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param Photo $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function __toString()
    {
        return '[PhotoStat'
        .' photo:'.$this->getPhoto()->getUser()->getUsername()
        .' rating:'.$this->getRating()
        .' views:'.$this->getViews()
        .' likes:'.$this->getLikes()
        .']';
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }
}
