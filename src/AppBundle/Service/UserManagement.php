<?php


namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Letter;
use AppBundle\Entity\Agenda;
use AppBundle\Entity\Team;
use AppBundle\Entity\Agent;
use AppBundle\Entity\AgendaTemp;

use Symfony\Component\Security\Core\User\UserInterface;




class UserManagement {
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    
    public function countWaitedValidation(UserInterface $user)
    {

        $agent =  $this->em
            ->getRepository(Agent::class)
            ->findOneBy(['username'=> $user->getUsername()]);

        $team = $agent->getTeam();
        $agentInTeam = $team->getAgents();
        $agentsId =  $this->em
            ->getRepository(AgendaTemp::class)
            ->findAgentIdByAgendaTemp($agentInTeam);


        $agentInTemp = $this->em
            ->getRepository(Agent::class)
            ->findMyAgent($agentsId);

        $countWaitedValidation = count ($agentInTemp);

        return $countWaitedValidation;


    }    
}
