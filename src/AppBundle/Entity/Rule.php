<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rule
 *
 * @ORM\Table(name="rule")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RuleRepository")
 */
class Rule
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
     * @var float
     *
     * @ORM\Column(name="maxHourPerDay", type="float")
     */
    private $maxHourPerDay;

    /**
     * @var float
     *
     * @ORM\Column(name="minRestPerWeek", type="float")
     */
    private $minRestPerWeek;

    /**
     * @var float
     *
     * @ORM\Column(name="minRestBetweenDays", type="float")
     */
    private $minRestBetweenDays;

    /**
     * @var float
     *
     * @ORM\Column(name="maxHourperWeek", type="float")
     */
    private $maxHourperWeek;

    /**
     * @var float
     *
     * @ORM\Column(name="maxAveragePerWeek", type="float")
     */
    private $maxAveragePerWeek;

    /**
     * @var float
     *
     * @ORM\Column(name="nbWeekForAverage", type="float")
     */
    private $nbWeekForAverage;


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
     * Set maxHourPerDay
     *
     * @param float $maxHourPerDay
     *
     * @return Rule
     */
    public function setMaxHourPerDay($maxHourPerDay)
    {
        $this->maxHourPerDay = $maxHourPerDay;

        return $this;
    }

    /**
     * Get maxHourPerDay
     *
     * @return float
     */
    public function getMaxHourPerDay()
    {
        return $this->maxHourPerDay;
    }

    /**
     * Set minRestPerWeek
     *
     * @param float $minRestPerWeek
     *
     * @return Rule
     */
    public function setMinRestPerWeek($minRestPerWeek)
    {
        $this->minRestPerWeek = $minRestPerWeek;

        return $this;
    }

    /**
     * Get minRestPerWeek
     *
     * @return float
     */
    public function getMinRestPerWeek()
    {
        return $this->minRestPerWeek;
    }

    /**
     * Set minRestBetweenDays
     *
     * @param float $minRestBetweenDays
     *
     * @return Rule
     */
    public function setMinRestBetweenDays($minRestBetweenDays)
    {
        $this->minRestBetweenDays = $minRestBetweenDays;

        return $this;
    }

    /**
     * Get minRestBetweenDays
     *
     * @return float
     */
    public function getMinRestBetweenDays()
    {
        return $this->minRestBetweenDays;
    }

    /**
     * Set maxHourperWeek
     *
     * @param float $maxHourperWeek
     *
     * @return Rule
     */
    public function setMaxHourperWeek($maxHourperWeek)
    {
        $this->maxHourperWeek = $maxHourperWeek;

        return $this;
    }

    /**
     * Get maxHourperWeek
     *
     * @return float
     */
    public function getMaxHourperWeek()
    {
        return $this->maxHourperWeek;
    }

    /**
     * Set maxAveragePerWeek
     *
     * @param float $maxAveragePerWeek
     *
     * @return Rule
     */
    public function setMaxAveragePerWeek($maxAveragePerWeek)
    {
        $this->maxAveragePerWeek = $maxAveragePerWeek;

        return $this;
    }

    /**
     * Get maxAveragePerWeek
     *
     * @return float
     */
    public function getMaxAveragePerWeek()
    {
        return $this->maxAveragePerWeek;
    }

    /**
     * Set nbWeekForAverage
     *
     * @param float $nbWeekForAverage
     *
     * @return Rule
     */
    public function setNbWeekForAverage($nbWeekForAverage)
    {
        $this->nbWeekForAverage = $nbWeekForAverage;

        return $this;
    }

    /**
     * Get nbWeekForAverage
     *
     * @return float
     */
    public function getNbWeekForAverage()
    {
        return $this->nbWeekForAverage;
    }
}

