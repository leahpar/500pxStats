<?php

namespace PX500\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserStats
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PX500\CoreBundle\Entity\UserStatsRepository")
 */
class UserStats
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
     * @return UserStats
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
     * @return UserStats
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
     * @return UserStats
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
}
