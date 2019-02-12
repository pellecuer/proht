<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Agent
 *
 * @ORM\Table(name="app_users")
 * @UniqueEntity(fields="email", message="Cet email éxiste déjà")
 * @UniqueEntity(fields="username", message="Ce nom d'utilisateur éxiste déjà")
 * @UniqueEntity(fields="nni", message="Ce NNI éxiste déjà")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgentRepository")
 */
class Agent implements UserInterface, \Serializable
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
     * @ORM\Column(name="Nni", type="string", length=45, nullable=true, unique=true)
     */
    private $nni;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="FirstName", type="string", length=100, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="Function", type="string", length=100, nullable=true)
     */
    private $function;
    
    
    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team", inversedBy="agents")
     * @ORM\JoinColumn(nullable=true)
     */
    private $team;
    
    /**
     * @ORM\Column(type="string", length=254, unique=true)
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank
     */
    private $username;
    
    
    /**
     * @Assert\NotBlank
     * @Assert\Length(max=4096)
     */
    private $plainPassword;
    
    
    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

   
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $roles;   

    
    
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
        return $this->team;
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

    function setTeam( Team $team) {
        $this->team = $team;
        return $this;
    }

    

    // other properties and methods

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
    }
    
    
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
    
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }
    

    public function getPassword()
    {
        return $this->password;
    }
    
     public function setPassword($password)
    {
        $this->password = $password;
    }
    
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_AGENT';
        return array_unique($roles);
    }
    
     public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }
    
    
    
}