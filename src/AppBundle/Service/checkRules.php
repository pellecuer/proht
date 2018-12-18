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
    
    
    
    public function restBetweenDays($agendaTemp, $user, $date, $letter, $arrayDays)
    {            
        $timeBeforeDate = $arrayDays[0]->getLetter()->getEndTime();
        $hBeforeDate = $timeBeforeDate->format('H');        
        $iBeforeDate = $timeBeforeDate->format('i');
        $immutableDay0 = \DateTimeImmutable::createFromMutable($arrayDays[0]->getDate());
        $dateTimeBeforeDate = $immutableDay0->setTime($hBeforeDate, $iBeforeDate);  

        $timeOnDate = $arrayDays[1]->getLetter()->getStartTime();
        $hOnDate = $timeOnDate->format('H');        
        $iOnDate = $timeOnDate->format('i');
        $immutableDay1 = \DateTimeImmutable::createFromMutable($arrayDays[1]->getDate());
        $dateTimeOnDate = $immutableDay1->setTime($hOnDate, $iOnDate);

        $timeOffDate = $arrayDays[1]->getLetter()->getEndTime();
        $hOffDate = $timeOffDate->format('H');        
        $iOffDate = $timeOffDate->format('i');            
        $dateTimeOffDate = $immutableDay1->setTime($hOffDate, $iOffDate);

        $timeAfterDate = $arrayDays[2]->getLetter()->getStartTime();
        $hAfterDate = $timeAfterDate->format('H');        
        $iAfterDate = $timeAfterDate->format('i');
        $immutableDay2 = \DateTimeImmutable::createFromMutable($arrayDays[2]->getDate());
        $dateTimeAfterDate = $immutableDay2->setTime($hAfterDate, $iAfterDate);

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


