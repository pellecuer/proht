<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Planning
 *
 * @ORM\Table(name="planning")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlanningRepository")
 */
class Planning
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
     * @ORM\Column(name="BeginDate", type="datetime")
     */
    private $beginDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateChange", type="datetime")
     */
    private $dateChange;    
      
    
    


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
     * Set beginDate
     *
     * @param \DateTime $beginDate
     *
     * @return Planning
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
     * Set dateChange
     *
     * @param \DateTime $dateChange
     *
     * @return Planning
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
}

