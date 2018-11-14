<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\OneToMany(targetEntity="Agent", mappedBy="Team")
     */
    private $Agents;

    public function __construct()
    {
        $this->agents = new ArrayCollection();
    }
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Event;
    
    

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
    
    function getAgents() {
        return $this->Agents;
    }

    function setAgents($Agents) {
        $this->Agents = $Agents;
    }

    function getEvent() {
        return $this->Event;
    }

    function setEvent($Event) {
        $this->Event = $Event;
    }
      
}

