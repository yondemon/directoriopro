<?php

namespace Application\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application\PlaceBundle\Entity\PlaceUser
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PlaceUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $place_id
     *
     * @ORM\Column(name="place_id", type="integer")
     */
    private $place_id;

    /**
     * @var integer $user_id
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


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
     * Set place_id
     *
     * @param integer $placeId
     */
    public function setPlaceId($placeId)
    {
        $this->place_id = $placeId;
    }

    /**
     * Get place_id
     *
     * @return integer 
     */
    public function getPlaceId()
    {
        return $this->place_id;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set date
     *
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
    }
}