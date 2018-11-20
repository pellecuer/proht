<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Section
 *
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 */
class Section
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="chief", type="string", length=255)
     */
    private $chief;
    
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Service")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Service;
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Event;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Section
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set chief
     *
     * @param string $chief
     *
     * @return Section
     */
    public function setChief($chief)
    {
        $this->chief = $chief;

        return $this;
    }

    /**
     * Get chief
     *
     * @return string
     */
    public function getChief()
    {
        return $this->chief;
    }
    
    function getService() {
        return $this->Service;
    }

    function getEvent() {
        return $this->Event;
    }

    function setService($Service) {
        $this->Service = $Service;
    }

    function setEvent($Event) {
        $this->Event = $Event;
    }


}

