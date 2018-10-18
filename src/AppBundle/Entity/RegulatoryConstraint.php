<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegulatoryConstraint
 *
 * @ORM\Table(name="regulatory_constraint")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RegulatoryConstraintRepository")
 */
class RegulatoryConstraint
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
     * @ORM\Column(name="MaxDurationPerDay", type="decimal", precision=10, scale=2)
     */
    private $maxDurationPerDay;

    /**
     * @var string
     *
     * @ORM\Column(name="MinWeekRest", type="decimal", precision=10, scale=2)
     */
    private $minWeekRest;

    /**
     * @var string
     *
     * @ORM\Column(name="MinRestBeetweenDays", type="decimal", precision=10, scale=2)
     */
    private $minRestBeetweenDays;

    /**
     * @var string
     *
     * @ORM\Column(name="MaxHourPerWeek", type="decimal", precision=10, scale=2)
     */
    private $maxHourPerWeek;

    /**
     * @var string
     *
     * @ORM\Column(name="MaxAverageHourPerWeek", type="decimal", precision=10, scale=2)
     */
    private $maxAverageHourPerWeek;

    /**
     * @var int
     *
     * @ORM\Column(name="NWeekCalculateAverage", type="smallint")
     */
    private $nWeekCalculateAverage;


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
     * Set maxDurationPerDay
     *
     * @param string $maxDurationPerDay
     *
     * @return RegulatoryConstraint
     */
    public function setMaxDurationPerDay($maxDurationPerDay)
    {
        $this->maxDurationPerDay = $maxDurationPerDay;

        return $this;
    }

    /**
     * Get maxDurationPerDay
     *
     * @return string
     */
    public function getMaxDurationPerDay()
    {
        return $this->maxDurationPerDay;
    }

    /**
     * Set minWeekRest
     *
     * @param string $minWeekRest
     *
     * @return RegulatoryConstraint
     */
    public function setMinWeekRest($minWeekRest)
    {
        $this->minWeekRest = $minWeekRest;

        return $this;
    }

    /**
     * Get minWeekRest
     *
     * @return string
     */
    public function getMinWeekRest()
    {
        return $this->minWeekRest;
    }

    /**
     * Set minRestBeetweenDays
     *
     * @param string $minRestBeetweenDays
     *
     * @return RegulatoryConstraint
     */
    public function setMinRestBeetweenDays($minRestBeetweenDays)
    {
        $this->minRestBeetweenDays = $minRestBeetweenDays;

        return $this;
    }

    /**
     * Get minRestBeetweenDays
     *
     * @return string
     */
    public function getMinRestBeetweenDays()
    {
        return $this->minRestBeetweenDays;
    }

    /**
     * Set maxHourPerWeek
     *
     * @param string $maxHourPerWeek
     *
     * @return RegulatoryConstraint
     */
    public function setMaxHourPerWeek($maxHourPerWeek)
    {
        $this->maxHourPerWeek = $maxHourPerWeek;

        return $this;
    }

    /**
     * Get maxHourPerWeek
     *
     * @return string
     */
    public function getMaxHourPerWeek()
    {
        return $this->maxHourPerWeek;
    }

    /**
     * Set maxAverageHourPerWeek
     *
     * @param string $maxAverageHourPerWeek
     *
     * @return RegulatoryConstraint
     */
    public function setMaxAverageHourPerWeek($maxAverageHourPerWeek)
    {
        $this->maxAverageHourPerWeek = $maxAverageHourPerWeek;

        return $this;
    }

    /**
     * Get maxAverageHourPerWeek
     *
     * @return string
     */
    public function getMaxAverageHourPerWeek()
    {
        return $this->maxAverageHourPerWeek;
    }

    /**
     * Set nWeekCalculateAverage
     *
     * @param integer $nWeekCalculateAverage
     *
     * @return RegulatoryConstraint
     */
    public function setNWeekCalculateAverage($nWeekCalculateAverage)
    {
        $this->nWeekCalculateAverage = $nWeekCalculateAverage;

        return $this;
    }

    /**
     * Get nWeekCalculateAverage
     *
     * @return int
     */
    public function getNWeekCalculateAverage()
    {
        return $this->nWeekCalculateAverage;
    }
}

