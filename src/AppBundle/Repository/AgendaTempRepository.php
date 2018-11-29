<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AgendaTemp;
use AppBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgendaTempRepository")
 */
class AgendaTempRepository extends EntityRepository
{
    
    /**
     * @return Agenda[] Returns an array of Agenda objects
     */
    public function findAgentBetweenDate($startDate, $endDate, $agent)
    {
        return $this->createQueryBuilder('a')
            ->where('a.date >= :start')
            ->andWhere('a.date <= :end')
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
    
    /**
     * @param $startDate, $endDate, $agendaId     
     */
    public function findAllBetweenDate($startDate, $endDate, $agentId)
    {
        return $this->createQueryBuilder('agendaTemp')
            ->where('agendaTemp.date >= :start')
            ->andWhere('agendaTemp.date <= :end')            
                
            ->innerJoin('agendaTemp.agent', 'agent')
            ->addSelect('agent')
            ->andWhere('agent.id = :id')    
                
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('id', $agentId)
            ->orderBy('agendaTemp.date', 'ASC')
            ->setMaxResults(10000)
            ->getQuery()
            ->getResult()
            ;
    }
    
    /**
     * @param $startDate, $endDate, $agendaId     
     */
    public function findAllTempBetweenDate($startDate, $endDate, $agentId)
    {
        return $this->createQueryBuilder('agendaTemp')
            ->where('agendaTemp.date >= :start')
            ->andWhere('agendaTemp.date <= :end')            
                
            ->innerJoin('agendaTemp.agent', 'agent')
            ->addSelect('agent')
            ->andWhere('agent.id = :id')    
                
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('id', $agentId)
            ->orderBy('agendaTemp.date', 'ASC')
            ->setMaxResults(10000)
            ->getQuery()
            ->getResult()
            ;
    }
    
    
    /**
     * @param $startDate, $endDate, $event
     * @return Event[]
     */
    public function findEventBetweenDate($startDate, $endDate)
    {
        return $this->createQueryBuilder('event')
            ->where('event.startDate >= :start')
            ->andWhere('event.endDate <= :end') 
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)            
            ->setMaxResults(10000)
            ->getQuery()
            ->getResult()
            ;
    }
    
    /**
     * @param $startDate, $endDate, $agendaId      
     */
    public function deleteAgentAgendaBetweenDate($startDate, $endDate, $agent)
    {
        return $this->createQueryBuilder('agenda')                       
            ->where('agenda.date >= :start')
            ->andWhere('agenda.date <= :end') 
            ->andWhere('agenda.agent = :agent')
                
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('agent', $agent)
            ->getQuery()            
            ->getResult()
            ;       
    }
}
