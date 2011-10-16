<?php

namespace Application\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application\UserBundle\Entity\Comment
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Comment
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
     * @var integer $from_id
     *
     * @ORM\Column(name="from_id", type="integer")
     */
    private $from_id;

    /**
     * @var integer $to_id
     *
     * @ORM\Column(name="to_id", type="integer")
     */
    private $to_id;

    /**
     * @var datetime $date
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var text $body
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var integer $type
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;


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
     * Set from_id
     *
     * @param integer $fromId
     */
    public function setFromId($fromId)
    {
        $this->from_id = $fromId;
    }

    /**
     * Get from_id
     *
     * @return integer 
     */
    public function getFromId()
    {
        return $this->from_id;
    }

    /**
     * Set to_id
     *
     * @param integer $toId
     */
    public function setToId($toId)
    {
        $this->to_id = $toId;
    }

    /**
     * Get to_id
     *
     * @return integer 
     */
    public function getToId()
    {
        return $this->to_id;
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

    /**
     * Set body
     *
     * @param text $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get body
     *
     * @return text 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set type
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
}