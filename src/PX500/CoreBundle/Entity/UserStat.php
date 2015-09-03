<?php

namespace PX500\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserStat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PX500\CoreBundle\Entity\UserStatRepository")
 */
class UserStat
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
     * @ORM\ManyToOne(targetEntity="PX500\CoreBundle\Entity\User", inversedBy="stats")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Photo
     * Photo added by that UserStat
     *
     * @ORM\ManyToOne(targetEntity="PX500\CoreBundle\Entity\Photo")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
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
     * @ORM\Column(name="affection", type="integer")
     */
    private $affection;

    /**
     * @var integer
     *
     * @ORM\Column(name="followers", type="integer")
     */
    private $followers;


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
     * @return UserStat
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
     * Set affection
     *
     * @param integer $affection
     * @return UserStat
     */
    public function setAffection($affection)
    {
        $this->affection = $affection;

        return $this;
    }

    /**
     * Get affection
     *
     * @return integer 
     */
    public function getAffection()
    {
        return $this->affection;
    }

    /**
     * Set followers
     *
     * @param integer $followers
     * @return UserStat
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;

        return $this;
    }

    /**
     * Get followers
     *
     * @return integer 
     */
    public function getFollowers()
    {
        return $this->followers;
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

    public function __toString()
    {
        return '[UserStat'
            .' user:'.$this->getUser()->getUsername()
            .' date:'.$this->getDate()->format('Y-m-d H:i:s')
            .' affection:'.$this->getAffection()
            .']';
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
}
