<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AgendaTemp;
use AppBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgendaTempRepository")
 */
class AgendaTempRepository extends EntityRepository
{
    
    /**
     * @return Agenda[] Returns an array of AgendaTemp objects
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
            ->setMaxResults(10000)
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
    
    /**
     * @return Agenda[] Returns an array of Agenda objects
     */
    public function findAgent($agent)
    {
        return $this->createQueryBuilder('a')           
            ->andWhere('a.agent = :agent')            
            ->setParameter('agent', $agent)  
            ->getQuery()
            ->getResult()
            ;
    }
    
    
    
    /**
     * @param $team, $user     
     */
    public function findTempByUserByTeam($team, $user)
    {
        return $this->createQueryBuilder('agendaTemp')
            ->where('agendaTemp.user = :user')               
            ->innerJoin('agendaTemp.agent', 'agent')
            ->addSelect('agent')
            ->andWhere('agent.team = :team')    
                
            ->setParameter('user', $user)
            ->setParameter('team', $team)
            
            ->getQuery()
            ->getResult()
            ;
    }
    
    /**
     * @param $team     
     */    
    public function findAgendaByTeam($team)
    {
        return $this->createQueryBuilder('agenda') 
           ->select('agenda')
           ->innerJoin('agenda.agent', 'agent')
           ->addSelect('agent')
           ->andWhere('agent.team = :team')
            
            ->setParameter('team', $team)
            
            ->getQuery()
            ->getResult()
            ;
    }
    
    /**
     * @param $startDate, $endDate, $agentId    
     */
    public function findAllTempBetweenDateByAgent($startDate, $endDate, $agent)
    {
        return $this->createQueryBuilder('agendaTemp')
            ->where('agendaTemp.agent = :agent')
            ->andWhere('agendaTemp.date >= :start')
            ->andWhere('agendaTemp.date <= :end') 
              
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('agent', $agent)
            ->orderBy('agendaTemp.date', 'ASC')            
            ->getQuery()
            ->getResult()
            ;
    }
    
    
    
    
    
    
    /**
     * @param $startDate, $endDate, $agent  
     */
    public function findTempBetweenDateByAgentByLetter($startDate, $endDate, $agent, $letter)
    {
        return $this->createQueryBuilder('agendaTemp')            
            ->Where('agendaTemp.date >= :start')
            ->andWhere('agendaTemp.date < :end')
            ->andWhere('agendaTemp.agent = :agent')
            ->andWhere('agendaTemp.letter = :letter') 
              
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('agent', $agent)
            ->setParameter('letter', $letter)                    
            ->getQuery()
            ->getResult()
            ;
    }
    
    
     /**
     * @param $startDate, $endDate, $agent  
     */
    public function findTempByDateByAgentByLetter($date, $agent, $letter)
    {
        return $this->createQueryBuilder('agendaTemp')            
            ->Where('agendaTemp.date = :date')            
            ->andWhere('agendaTemp.agent = :agent')
            ->andWhere('agendaTemp.letter = :letter')
               
            ->setParameter('date', $date)            
            ->setParameter('agent', $agent)
            ->setParameter('letter', $letter)                    
            ->getQuery()
            ->getResult()
            ;
    }


    /**
     * @param $startDate, $Date, $agent
     */
    public function findTempByDateByAgent($date, $agent)
    {
        return $this->createQueryBuilder('agendaTemp')
            ->where('agendaTemp.agent = :agent')
            ->andWhere('agendaTemp.date = :date')  
            
            ->setParameter('date', $date)
            ->setParameter('agent', $agent)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
    * @param $agent     
    */    
   public function findTempByAgent($agent)
   {
       return $this->createQueryBuilder('agendaTemp') 
           ->where('agendaTemp.agent = :agent')
           ->setParameter('agent', $agent)
           ->getQuery()
           ->getResult()
           ;
   }

    /**
     * @param $agentInTeam
     */
    public function findAgentIdByAgendaTemp($agentInTeam)
    {
        return $this->createQueryBuilder('agendaTemp')
            ->innerJoin('agendaTemp.agent', 'agent')
            ->Select('agent.id')
            ->where('agendaTemp.agent IN (:agent)')
            ->setParameter('agent', $agentInTeam)
            ->distinct()
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $agentsId
     */
    public function findMyAgent($agentsId)
    {
        return $this->createQueryBuilder('agent')
            ->where('agent.id  IN (:agentsId)')
            ->setParameter('agentsId', $agentsId)
            ->getQuery()
            ->getResult()
            ;
    }

}
