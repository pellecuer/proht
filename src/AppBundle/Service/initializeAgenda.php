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
    
     public function getHappyMessage()
    {
        $messages = [
            'You did it! You updated the system! Amazing!',
            'That was one of the coolest updates I\'ve seen all day!',
            'Great work! Keep going!',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }
    
    
    public function __construct(EntityManagerInterface $entityManager)
{
    $this->em = $entityManager;
}
    
    public function initialize($team, $agent)
    {
        //build the arrayDate
        $startDate = $team->getEvent()->getStartDate();
        $endDate = $team->getEvent()->getEndDate();
        $letter = $this->em->getRepository(Letter::class)->find(1);
        //$letter =  $this->getDoctrine()->getRepository(Letter::class)->findOneByName('L');
        
        
        $date = $startDate;  
        
        while ($startDate<$endDate){ 
            $agenda = new Agenda();
            $agenda->setAgent($agent);           
            $agenda->setDate($date);
            
            $dayWeek =$date->format('w');
            
            if ($dayWeek == 6) {
                $idLetter = 22;
            } elseif ($dayWeek == 0) {
                $idLetter = 23;
            } else {
                $idLetter = 1;
            }
            $letter = $this->em->getRepository(Letter::class)->find($idLetter);
                 
           $agenda->setletter($letter);           
           
           $this->em->persist($agenda);      
           
           //increment date
           $date -> modify('+1 day');
        }
        
        $this->em->flush();
        $message = 'agenda initialisé pour l\'agent : ' . $agent->getName();

        return $message;
    }
    
}
