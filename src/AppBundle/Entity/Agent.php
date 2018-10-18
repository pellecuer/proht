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
     * @ORM\Column(name="NNI", type="string", length=45)
     */
    private $nNI;

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
     * @var string
     *
     * @ORM\Column(name="Role", type="string", length=100)
     */
    private $role;


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
     * Set nNI
     *
     * @param string $nNI
     *
     * @return Agent
     */
    public function setNNI($nNI)
    {
        $this->nNI = $nNI;

        return $this;
    }

    /**
     * Get nNI
     *
     * @return string
     */
    public function getNNI()
    {
        return $this->nNI;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Agent
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Agent
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set function
     *
     * @param string $function
     *
     * @return Agent
     */
    public function setFunction($function)
    {
        $this->function = $function;

        return $this;
    }

    /**
     * Get function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return Agent
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
}

