<?php


namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Letter;
use AppBundle\Entity\Agenda;



/**
 * Description of initializeAgenda
 *
 * @author E32979
 */
class initializeAgenda {
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    
    public function initialize($team, $agent)
    {
        //build the arrayDate
        $startDate = $team->getEvent()->getStartDate();        
        $endDate = $team->getEvent()->getEndDate();        
        $date = $startDate;
        $agentId = $agent->getId();
        
        //remove existent agenda if exists
        $agendaToRemoves = $this->em
            ->getRepository(Agenda::class)
            ->deleteAgentAgendaBetweenDate($startDate, $endDate, $agentId);
        
        if ($agendaToRemoves) {
        
        foreach ($agendaToRemoves as $agendaToRemove){
            $this->em->remove($agendaToRemove); 
        }                
        $this->em->flush();        
        } 
        
        //initialize Agenda with default values        
        while ($startDate<=$endDate){ 
            $agenda = new Agenda();
            $agenda->setAgent($agent);           
            $agenda->setDate($date);            
            
            $dayWeek =$date->format('w');
            //if saturday
            if ($dayWeek == 6) {                
                $letterName = 'R';
            
            //if sunday    
            } elseif ($dayWeek == 0) {
                $letterName = 'H';
                
            //other dayw        
            } else {
                $letterName = 'J';
            }
            $letter = $this->em
                    ->getRepository(Letter::class)
                    ->findOneBy([
                        'letter' => $letterName
                        ]);
                 
           $agenda->setletter($letter);
           $this->em->persist($agenda);
           $this->em->flush();
           $date -> modify('+1 day');           
        }        
    }    
}
