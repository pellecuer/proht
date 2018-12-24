<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\AgendaTemp;



class checkRules {
    
    public function __construct(EntityManagerInterface $entityManager)
{
    $this->em = $entityManager;
}
    
    public function HoursPerWeek($agendaTemp, $user, $startLegalWeek, $endLegalWeek, $arrayWeeks)
    {        
        $hoursPerWeek = 0;        
        foreach ($arrayWeeks as $arrayWeek){
            if ($arrayWeek->getLetter()->getLetter() == 'R' || $arrayWeek->getLetter()->getLetter() == 'H') {
                $effectiveDuration = 0;
            } else {
                $effectiveDuration = $arrayWeek->getLetter()->getEffectiveDuration();
            }
            $hoursPerWeek = $hoursPerWeek + $effectiveDuration;
        }
        
        return $hoursPerWeek;
    }
    
    
    
    public function restBetweenDays($agendaTemp, $user, $date, $letter, $agendaTempAround)
    {            
        //if DateTemp is the first record of the the period, set $dateTimeBeforeDate to -11h        
        if ($agendaTemp->getDate() == $agendaTemp->getAgent()->getTeam()->getEvent()->getStartDate()){
        
            //Define startTime of the date
            $timeOnDate = $agendaTempAround[0]->getLetter()->getStartTime();
            $hOnDate = $timeOnDate->format('H');        
            $iOnDate = $timeOnDate->format('i');
            $immutableDay1 = \DateTimeImmutable::createFromMutable($agendaTempAround[0]->getDate());
            $dateTimeOnDate = $immutableDay1->setTime($hOnDate, $iOnDate);
            
            //Define EndTime of the date before
            $dateTimeBeforeDate = $dateTimeOnDate->modify('-11 hour');
            
            //Define endTime of the Date
            $timeOffDate = $agendaTempAround[0]->getLetter()->getEndTime();
            $hOffDate = $timeOffDate->format('H');        
            $iOffDate = $timeOffDate->format('i');            
            $dateTimeOffDate = $immutableDay1->setTime($hOffDate, $iOffDate);
            
            //Define startTime of the date After
            $timeAfterDate = $agendaTempAround[1]->getLetter()->getStartTime();
            $hAfterDate = $timeAfterDate->format('H');        
            $iAfterDate = $timeAfterDate->format('i');
            $immutableDay2 = \DateTimeImmutable::createFromMutable($agendaTempAround[1]->getDate());
            $dateTimeAfterDate = $immutableDay2->setTime($hAfterDate, $iAfterDate);            
        }
        
        //if DateTemp is the last record of the the period, set $dateTimeAfterDate to +11h        
        elseif ($agendaTemp->getDate() == $agendaTemp->getAgent()->getTeam()->getEvent()->getEndDate()){    
            
            //Define endTime before Date
            $timeBeforeDate = $agendaTempAround[0]->getLetter()->getEndTime();
            $hBeforeDate = $timeBeforeDate->format('H');        
            $iBeforeDate = $timeBeforeDate->format('i');
            $immutableDay0 = \DateTimeImmutable::createFromMutable($agendaTempAround[0]->getDate());
            $dateTimeBeforeDate = $immutableDay0->setTime($hBeforeDate, $iBeforeDate);  
            
            //Define startTime of the date
            $timeOnDate = $agendaTempAround[1]->getLetter()->getStartTime();
            $hOnDate = $timeOnDate->format('H');        
            $iOnDate = $timeOnDate->format('i');
            $immutableDay1 = \DateTimeImmutable::createFromMutable($agendaTempAround[1]->getDate());
            $dateTimeOnDate = $immutableDay1->setTime($hOnDate, $iOnDate);
            
            //Define endTime of the Date
            $timeOffDate = $agendaTempAround[1]->getLetter()->getEndTime();
            $hOffDate = $timeOffDate->format('H');        
            $iOffDate = $timeOffDate->format('i');            
            $dateTimeOffDate = $immutableDay1->setTime($hOffDate, $iOffDate);  
            
            //Define startTime after Date
            $dateTimeAfterDate = $dateTimeOffDate->modify('+11 hour');
        }
        
        // Defines variable between array[0,1,2]
        else {
            $timeBeforeDate = $agendaTempAround[0]->getLetter()->getEndTime();
            $hBeforeDate = $timeBeforeDate->format('H');        
            $iBeforeDate = $timeBeforeDate->format('i');
            $immutableDay0 = \DateTimeImmutable::createFromMutable($agendaTempAround[0]->getDate());
            $dateTimeBeforeDate = $immutableDay0->setTime($hBeforeDate, $iBeforeDate);   

            $timeOnDate = $agendaTempAround[1]->getLetter()->getStartTime();
            $hOnDate = $timeOnDate->format('H');        
            $iOnDate = $timeOnDate->format('i');
            $immutableDay1 = \DateTimeImmutable::createFromMutable($agendaTempAround[1]->getDate());
            $dateTimeOnDate = $immutableDay1->setTime($hOnDate, $iOnDate);

            $timeOffDate = $agendaTempAround[1]->getLetter()->getEndTime();
            $hOffDate = $timeOffDate->format('H');        
            $iOffDate = $timeOffDate->format('i');            
            $dateTimeOffDate = $immutableDay1->setTime($hOffDate, $iOffDate); 

            $timeAfterDate = $agendaTempAround[2]->getLetter()->getStartTime();
            $hAfterDate = $timeAfterDate->format('H');        
            $iAfterDate = $timeAfterDate->format('i');
            $immutableDay2 = \DateTimeImmutable::createFromMutable($agendaTempAround[2]->getDate());
            $dateTimeAfterDate = $immutableDay2->setTime($hAfterDate, $iAfterDate);
        }

        //Important
        $intervalBefore = $dateTimeBeforeDate->diff($dateTimeOnDate)->format('%H:%I:%S');
        $intervalAfter = $dateTimeOffDate->diff($dateTimeAfterDate)->format('%H:%I:%S');           
        
        $interval = [
            $intervalBefore,
            $intervalAfter
        ];
        
        return $interval;        
    }
}


