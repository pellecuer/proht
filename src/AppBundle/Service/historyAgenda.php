<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\HistoryChange;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


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
    
    public function history($agenda, $connectedUser, $letter)
    {        
        $history = new HistoryChange();
        $history->setDateChange(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $history->setDate($agenda->getDate());
        $history->setAgent($agenda->getAgent());
        $history->setLetter($letter);
        $history->setValideur($connectedUser);     
        $this->em->persist($history);
        $this->em->flush();
    }    
}


