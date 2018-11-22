<?php


namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;



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
        $letter =  $this->getDoctrine()->getRepository(Letter::class)->findOneByName('L');
        
        //Build
        $date = $startDate;
        $entityManager = $this->getDoctrine()->getManager();
        while ($startDate<$endDate){
           
           $agenda = new Agenda();
           $agenda->setAgent($agent);
           $agenda->setletter($letter);
           $agenda->setDate($date);           
           
           //persist
           $entityManager->persist($agenda);          
           
           //increment date
           $date = $date++;
        }
        
        $entityManager->flush();
        $message = 'agenda initialisé pour l\'agent : ' . $agent->getName();

        return $message;
    }
    
}
