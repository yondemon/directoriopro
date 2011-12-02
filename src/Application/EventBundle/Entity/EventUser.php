<?php

namespace Application\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application\EventBundle\Entity\EventUser
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class EventUser
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
     * @var integer $event_id
     *
     * @ORM\Column(name="event_id", type="integer")
     */
    private $event_id;

    /**
     * @var integer $user_id
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;


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
     * Set event_id
     *
     * @param integer $eventId
     */
    public function setEventId($eventId)
    {
        $this->event_id = $eventId;
    }

    /**
     * Get event_id
     *
     * @return integer 
     */
    public function getEventId()
    {
        return $this->event_id;
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
}