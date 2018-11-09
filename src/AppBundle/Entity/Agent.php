<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Agent
 *
 * @ORM\Table(name="agent")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgentRepository")
 */
class Agent
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
     * @ORM\Column(name="Nni", type="string", length=45)
     */
    private $nni;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="FirstName", type="string", length=100)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="Function", type="string", length=100)
     */
    private $function;
    
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Team;
    

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Role")
     * * @ORM\JoinColumn(nullable=false)
     */
    private $role;
    
    function getId() {
        return $this->id;
    }

    function getNni() {
        return $this->nni;
    }

    function getName() {
        return $this->name;
    }

    function getFirstName() {
        return $this->firstName;
    }

    function getFunction() {
        return $this->function;
    }

    function getTeam() {
        return $this->Team;
    }

    function getRole() {
        return $this->role;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNni($nni) {
        $this->nni = $nni;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    function setFunction($function) {
        $this->function = $function;
    }

    function setTeam($Team) {
        $this->Team = $Team;
    }

    function setRole($role) {
        $this->role = $role;
    }
}