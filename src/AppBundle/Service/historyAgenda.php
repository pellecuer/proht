<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\HistoryChange;


/**
 * Description of initializeAgenda
 *
 * @author E32979
 */
class historyAgenda {
    
    public function __construct(EntityManagerInterface $entityManager)
{
    $this->em = $entityManager;
}
    
    public function history($agenda, $user)
    {
        $history = new HistoryChange();
        $history->setDateChange(new \DateTime('now'));
        $history->setDate($agenda->getDate());
        $history->setAgent($agenda->getAgent());
        $history->setLetter($agenda->getLetter());
        $history->setUser($user);
         
        $this->em->persist($history);
        $this->em->flush();
    }    
}


