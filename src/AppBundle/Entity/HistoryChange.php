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
     * @var string
     *
     * @ORM\Column(name="nni", type="string", length=45)
     */
    private $nni;

    /**
     * @var string
     *
     * @ORM\Column(name="letter", type="string", length=255)
     */
    private $letter;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateChange", type="datetime")
     */
    private $dateChange;

    /**
     * @var string
     *
     * @ORM\Column(name="nniValidator", type="string", length=45)
     */
    private $nniValidator;


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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return HistoryChange
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set nni
     *
     * @param string $nni
     *
     * @return HistoryChange
     */
    public function setNni($nni)
    {
        $this->nni = $nni;

        return $this;
    }

    /**
     * Get nni
     *
     * @return string
     */
    public function getNni()
    {
        return $this->nni;
    }

    /**
     * Set letter
     *
     * @param string $letter
     *
     * @return HistoryChange
     */
    public function setLetter($letter)
    {
        $this->letter = $letter;

        return $this;
    }

    /**
     * Get letter
     *
     * @return string
     */
    public function getLetter()
    {
        return $this->letter;
    }

    /**
     * Set dateChange
     *
     * @param \DateTime $dateChange
     *
     * @return HistoryChange
     */
    public function setDateChange($dateChange)
    {
        $this->dateChange = $dateChange;

        return $this;
    }

    /**
     * Get dateChange
     *
     * @return \DateTime
     */
    public function getDateChange()
    {
        return $this->dateChange;
    }

    /**
     * Set nniValidator
     *
     * @param string $nniValidator
     *
     * @return HistoryChange
     */
    public function setNniValidator($nniValidator)
    {
        $this->nniValidator = $nniValidator;

        return $this;
    }

    /**
     * Get nniValidator
     *
     * @return string
     */
    public function getNniValidator()
    {
        return $this->nniValidator;
    }
}

