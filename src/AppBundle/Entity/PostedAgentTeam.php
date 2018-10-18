<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostedAgentTeam
 *
 * @ORM\Table(name="posted_agent_team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostedAgentTeamRepository")
 */
class PostedAgentTeam
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
     * @return PostedAgentTeam
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
}

