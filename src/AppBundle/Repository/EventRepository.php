<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class EventRepository extends EntityRepository
{
    /**
     * @param $startDate, $endDate, $event
     * @return Event[]
     */
    public function findEventBetweenDate($startDate, $endDate)
    {
        return $this->createQueryBuilder('event')
            ->where('event.startDate > :start')
            ->andWhere('event.endDate < :end') 
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)            
            ->setMaxResults(10000)
            ->getQuery()
            ->getResult()
            ;
    }  
}
