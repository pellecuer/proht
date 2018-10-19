<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CodePlanning
 *
 * @ORM\Table(name="code_planning")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CodePlanningRepository")
 */
class CodePlanning
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
     * @ORM\Column(name="Letter", type="string", length=2)
     */
    private $letter;

    /**
     * @var string
     *
     * 
     * @ORM\Column(name="TimeRange", type="decimal", precision=10, scale=2)
     */
    private $timeRange;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @Assert\NotBlank()
     * @ORM\Column(name="BeginDate", type="datetime")
     */
    private $beginDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EndDate", type="datetime")
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="EffectiveDuration", type="decimal", precision=10, scale=2)
     */
    private $effectiveDuration;


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
     * Set letter
     *
     * @param string $letter
     *
     * @return CodePlanning
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
     * Set timeRange
     *
     * @param string $timeRange
     *
     * @return CodePlanning
     */
    public function setTimeRange($timeRange)
    {
        $this->timeRange = $timeRange;

        return $this;
    }

    /**
     * Get timeRange
     *
     * @return string
     */
    public function getTimeRange()
    {
        return $this->timeRange;
    }

    /**
     * Set beginDate
     *
     * @param \DateTime $beginDate
     *
     * @return CodePlanning
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;

        return $this;
    }

    /**
     * Get beginDate
     *
     * @return \DateTime
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return CodePlanning
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set effectiveDuration
     *
     * @param string $effectiveDuration
     *
     * @return CodePlanning
     */
    public function setEffectiveDuration($effectiveDuration)
    {
        $this->effectiveDuration = $effectiveDuration;

        return $this;
    }

    /**
     * Get effectiveDuration
     *
     * @return string
     */
    public function getEffectiveDuration()
    {
        return $this->effectiveDuration;
    }
}

