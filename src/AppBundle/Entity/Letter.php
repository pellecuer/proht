<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * CodePlanning
 *
 * @ORM\Table(name="Letter")
 * @UniqueEntity(fields="letter", message="La lettre éxiste déjà")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LetterRepository")
 */
class Letter
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
     * @ORM\Column(name="Letter", type="string", length=2, unique =true)
     */
    private $letter;
   

    /**
     * @var 
     *
     * @Assert\Time()     
     * @ORM\Column(name="startTime", type="time", nullable =true)
     */
    private $startTime;

    /**
     * @var
     *
     * @ORM\Column(name="EndTime", type="time", nullable =true)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="EffectiveDuration", type="decimal", precision=10, scale=2, nullable =true)
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
     * Set startTime
     *
     * @param \Time $startTime
     *
     * @return startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \Time
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \Time $endTime
     *
     * @return \Time
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \Time
     */
    public function getEndTime()
    {
        return $this->endTime;
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

