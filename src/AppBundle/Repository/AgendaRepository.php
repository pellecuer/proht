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
            ->setMaxResults(90)
            ->getQuery()
            ->getResult()
            ;
    }
    
    /**
     * @param $startDate, $endDate, $agent, $agendaId
     * @return Product[]
     */
    public function findAllBetweenDate($startDate, $endDate, $agendaId)
    {
        return $this->createQueryBuilder('a')
            ->where('a.date > :start')
            ->andWhere('a.date < :end')
            ->andWhere('a.id = :id')
                
            ->innerJoin('a.agent', 'g')
            ->addSelect('g')                
                
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('id', $agendaId)
            ->orderBy('a.date', 'ASC')
            ->setMaxResults(90)
            ->getQuery()
            ->getResult()
            ;
    }
    
    

    public function findByIdJoinedToAgent($agendaId, $endDate, $startDate)
    {
        return $this->createQueryBuilder('p')
            // g.agent refers to the "agent" property on agenda
            ->innerJoin('p.agent', 'g')
            // selects all the agent data to avoid the query
            ->addSelect('g')
            ->where('p.date > :start')
            ->andWhere('p.date < :end')
            ->andWhere('g.id = :id')
            ->setParameter('id', $agendaId)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('g.date', 'ASC')
            ->getQuery()
            ->getResult();
    }


}

