<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoryChange
 *
 * @ORM\Table(name="history_change")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistoryChangeRepository")
 */
class HistoryChange
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Agent")
     */
    private $agent;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Letter")
     */
    private $letter;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateChange", type="datetime")
     */
    private $dateChange;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $user;


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

    function getDateChange(): \DateTime {
        return $this->dateChange;
    }

    function getUser() {
        return $this->user;
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

    function setDateChange(\DateTime $dateChange) {
        $this->dateChange = $dateChange;
    }

    function setUser($user) {
        $this->user = $user;
    }    
}
