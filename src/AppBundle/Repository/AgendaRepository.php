<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Agenda;
use Doctrine\ORM\EntityRepository;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgendaRepository")
 */
class AgendaRepository extends EntityRepository
{
    
     /**
     * @return Agenda[] Returns an array of Agenda objects
     */
    public function findDateBetweenDate($startDate, $endDate)
    {
        return $this->createQueryBuilder('a')
            ->where('a.date > :start')
            ->andWhere('a.date < :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('a.date', 'ASC')
            ->setMaxResults(90)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Agenda[] Returns an array of Agenda objects
     */
    public function findAgentBetweenDate($startDate, $endDate, $agent)
    {
        return $this->createQueryBuilder('a')
            ->where('a.date > :start')
            ->andWhere('a.date < :end')
            ->andWhere('a.agent = :agent')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('agent', $agent)
            ->orderBy('a.date', 'ASC')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult()
            ;
    }
}

