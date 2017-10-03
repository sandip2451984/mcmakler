<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * @MongoDB\Document(repositoryClass="AppBundle\Repository\NeoRepository")
 */
class NeoDocument  
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="int")
     * @MongoDB\UniqueIndex
     */
    protected $reference;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $name;
     
    /**
     * @MongoDB\Field(type="float")
     */
    protected $speed;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $date;

    /**
     * @MongoDB\Field(type="bool")
     */
    protected $is_hazardous;

    /**
     * Get id
     *
     * @return id $id
     */
     public function getId()
     {
         return $this->id;
     }

    /**
     * Get reference
     *
     * @return int $reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set name
     *
     * @param int $reference
     * @return $this
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set speed
     *
     * @param float $speed
     * @return $this
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
        return $this;
    }

    /**
     * Get speed
     *
     * @return float $speed
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Set date
     *
     * @param date $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return date $date
     */
    public function getDate()
    {
        return $this->date->format('Y-m-d');
    }

    /**
     * Set isHazardous
     *
     * @param bool $isHazardous
     * @return $this
     */
    public function setIsHazardous($isHazardous)
    {
        $this->is_hazardous = $isHazardous;
        return $this;
    }

    /**
     * Get isHazardous
     *
     * @return bool $isHazardous
     */
    public function getIsHazardous()
    {
        return $this->is_hazardous;
    }
}
