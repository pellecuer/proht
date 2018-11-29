<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Agenda
 *
 * @ORM\Table(name="agendaTemp")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgendaTempRepository")
 * 
 */
class AgendaTemp
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Agent", cascade={"persist", "remove"})
     */
    private $agent;

    /**
     * @var 
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Letter")
     */
    private $letter;
    
    
    /**
     * @var 
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur")
     */
    private $utilisateur;


    function getId() {
        return $this->id;
    }

    function getDate(): \DateTime {
        return $this->date;
    }

    function getAgent() {
        return $this->agent;
    }

    function getLetter() {
        return $this->letter;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setDate(\DateTime $date) {
        $this->date = $date;
    }

    function setAgent($agent) {
        $this->agent = $agent;
    }

    function setLetter($letter) {
        $this->letter = $letter;
    }
    
    function getUtilisateur() {
        return $this->utilisateur;
    }

    function setUtilisateur($utilisateur) {
        $this->utilisateur = $utilisateur;
    }

}

