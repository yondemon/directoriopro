<?php

namespace Application\CityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application\CityBundle\Entity\City
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class City
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=200)
     */
    private $name;

    /**
     * @var string $alternate_names
     *
     * @ORM\Column(name="alternate_names", type="string", length=2000)
     */
    private $alternate_names;

    /**
     * @var long $lat
     *
     * @ORM\Column(name="lat", type="float")
     */
    private $lat;

    /**
     * @var float $lon
     *
     * @ORM\Column(name="lon", type="float")
     */
    private $lon;

    /**
     * @var string $code
     *
     * @ORM\Column(name="code", type="string", length=2)
     */
    private $code;

    /**
     * @var integer $population
     *
     * @ORM\Column(name="population", type="integer", length=11)
     */
    private $population;

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
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get alternate_names
     *
     * @return string 
     */
    public function getAlternateNames()
    {
        return $this->alternate_names;
    }

    /**
     * Get lat
     *
     * @return float 
     */
    public function getLat()
    {
        return $this->lat;
    }

	/**
     * Get lon
     *
     * @return float 
     */
    public function getLon()
    {
        return $this->lon;
    }

	/**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

	/**
     * Get population
     *
     * @return integer 
     */
    public function getPopulation()
    {
        return $this->population;
    }
}