<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Team
 *
 * @ORM\Table(name="Team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 */
class Team
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
     * @ORM\Column(name="name", type="string", length=45)
     */
    private $name;
    
    
    
    /**
     * @ORM\OneToMany(targetEntity="Agent", mappedBy="team")
     * @ORM\JoinColumn(nullable=true)
     */
    private $agents;
    
    public function __construct()
    {
        $this->agents = new ArrayCollection();
    }

    /**
     * @return Collection|Product[]
     */
    public function getAgents()
    {
        return $this->agents;
    }
    
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event")
     * @ORM\JoinColumn(nullable=true)
     */
    private $Event;
    
     /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Section")
     * @ORM\JoinColumn(nullable=true)
     */
    private $Section;
    

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    } 
    
    function getAgent() {
        return $this->Agent;
    }

    function setAgent($Agent) {
        $this->Agent = $Agent;
    }

    function getEvent() {
        return $this->Event;
    }

    function setEvent($Event) {
        $this->Event = $Event;
    }
    
    function getSection() {
        return $this->Section;
    }

    function setSection($Section) {
        $this->Section = $Section;
    }      
}
