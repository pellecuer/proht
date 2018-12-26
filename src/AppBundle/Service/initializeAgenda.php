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
        
        while ($startDate<=$endDate){ 
            $agenda = new Agenda();
            $agenda->setAgent($agent);           
            $agenda->setDate($date);            
            
            $dayWeek =$date->format('w');            
            if ($dayWeek == 6) {                
                $idLetter = 2;
            } elseif ($dayWeek == 0) {
                $idLetter = 3;
            } else {
                $idLetter = 1;
            }
            $letter = $this->em->getRepository(Letter::class)->find($idLetter);
                 
           $agenda->setletter($letter);
           $this->em->persist($agenda);
           $this->em->flush();
           $date -> modify('+1 day');           
        }
        
        
        $message = 'agenda initialisé pour l\'agent : ' . $agent->getName();

        return $message;
    }    
}
