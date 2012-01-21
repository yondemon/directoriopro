<?php

namespace Application\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application\TestBundle\Entity\TestUser
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TestUser
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
     * @var integer $test_id
     *
     * @ORM\Column(name="test_id", type="integer")
     */
    private $test_id;

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
     * Set test_id
     *
     * @param integer $testId
     */
    public function setTestId($testId)
    {
        $this->test_id = $testId;
    }

    /**
     * Get test_id
     *
     * @return integer 
     */
    public function getTestId()
    {
        return $this->test_id;
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