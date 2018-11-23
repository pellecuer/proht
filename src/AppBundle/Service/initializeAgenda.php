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
        $interval = new \DateInterval('P1D');
        $interval = date_diff($startDate, $endDate);
        
        while ($startDate<$endDate){           
           $date->add($interval);
           $agenda = new Agenda();
           $agenda->setAgent($agent);
           $agenda->setletter($letter);
           $agenda->setDate($date);          
           
           //persist
           //dump($agenda);die;
           $this->em->persist($agenda);       
           
           //increment date
           $date = $date++;
        }
        
        $this->em->flush();
        $message = 'agenda initialisé pour l\'agent : ' . $agent->getName();

        return $message;
    }
    
}
