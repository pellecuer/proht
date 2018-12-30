<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\AgendaTemp;
use AppBundle\Entity\Letter;


class checkRules {
    
    public function __construct(EntityManagerInterface $entityManager)
{
    $this->em = $entityManager;    
}



    public function StartLegalWeek($date)
    {        
        if ($date->format('w') == 0) {
            $startLegalWeek = $date->modify('sunday 00:00');
        }else {
            $startLegalWeek = $date->modify('last sunday 00:00');
        }
        
        return $startLegalWeek;
    }
    
    public function SendLegalWeek($date)
    {        
        $endLegalWeek = $date->modify('next sunday 00:00');
        
        return $endLegalWeek;
    }
    
    
    
    public function HoursPerWeek($arrayWeeks)
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


    
    public function averageHourPerWeek($agendaTemp, CheckRules $checkRules, $user, $arrayWeeks)
    {        
        $startEventDate = $agendaTemp->getAgent()->getTeam()->getEvent()->getStartDate();
        $endEventDate = $agendaTemp->getAgent()->getTeam()->getEvent()->getEndDate();
        $dateofWeek = \DateTimeImmutable::createFromMutable($startEventDate);
        $countEmptyWeek = 0;
        $SumHoursPerWeek = 0;

            //check if empty Week
            if ($dateofWeek->format('w') == 0) {
                $startLegalWeek = $dateofWeek->modify('sunday 00:00');
            } else {
                $startLegalWeek = $dateofWeek->modify('last sunday 00:00');
            }
            $endLegalWeek = $dateofWeek->modify('next sunday 00:00');

            $AgendaTempStartLegalWeek = $this->em
                ->getRepository(AgendaTemp::class)
                ->findTempByDateByUserByAgent($startLegalWeek, $agendaTemp->getAgent(), $user);

            $AgendaTempEndLegalWeek = $this->em
                ->getRepository(AgendaTemp::class)
                ->findTempByDateByUserByAgent($endLegalWeek, $agendaTemp->getAgent(), $user);


            if ($AgendaTempStartLegalWeek && $AgendaTempEndLegalWeek) {
                $hoursPerWeek = $checkRules->HoursPerWeek($agendaTemp, $user, $startLegalWeek, $endLegalWeek, $arrayWeeks);
                $SumHoursPerWeek += $hoursPerWeek;
                $countEmptyWeek = $countEmptyWeek + 1;
                $dateofWeek->modify('+ 7 days');
            }

            $averageHourPerWeek =  $SumHoursPerWeek;
            return $averageHourPerWeek;
    }


    public function RestBetweenDays($date, $user, $agent, $agendaTemp)
    {
        //check the dateBefore
        $dayBefore = $date->modify('yesterday');
        $AgendaTempBefore = $this->em
            ->getRepository(AgendaTemp::class)
            ->findOneBy([
                'agent' => $agent,
                'date' => $dayBefore,
                'user' => $user
            ]);

        //if no DayBefore or if letter before = R Or H, set intervalBefore to minimum legal (11h)
        $HLetter = $this->em
            ->getRepository(Letter::class)
            ->findByLetter('H');
        $RLetter = $this->em
            ->getRepository(Letter::class)
            ->findByLetter('R');

        if (!$AgendaTempBefore || $AgendaTempBefore->getLetter()->getLetter() == 'H' || $AgendaTempBefore->getLetter()->getLetter() == 'R')  {
        $intervalBefore =  new \DateInterval('PT11H');
        }

         //check the dateAfter
        $dayAfter = $date->modify('tomorrow');
        $AgendaTempAfter = $this->em
             ->getRepository(AgendaTemp::class)
             ->findOneBy([
                 'agent' => $agent,
                 'date' => $dayAfter,
                 'user' => $user
             ]);

         //if no DayAfter or if letter before = R Or H, set intervalBefore to minimum legal (11h)
        if (!$AgendaTempAfter || $AgendaTempAfter->getLetter() == $HLetter || $AgendaTempAfter->getLetter()->getLetter() == $RLetter)  {
        $intervalAfter =  new \DateInterval('PT11H');
        }


        //in other cases check interval before and after
        if (!isset ($intervalBefore) || !isset ($intervalAfter)) {

            $immutableDay = \DateTimeImmutable::createFromMutable($agendaTemp->getDate());

            //set startDateTime Day on date
            $startTimeDate = $agendaTemp->getLetter()->getStartTime();
            $hourStartTimeDate = $startTimeDate->format('H');
            $minuteStartTimeDate = $startTimeDate->format('i');
            $startDateTimeDay = $immutableDay->setTime($hourStartTimeDate, $minuteStartTimeDate);

            //set endDateTime Day on date
            $endTimeDate = $agendaTemp->getLetter()->getEndTime();
            $hourEndTimeDate = $endTimeDate->format('H');
            $minuteEndTimeDate = $endTimeDate->format('i');
            $endDateTimeDay = $immutableDay->setTime($hourEndTimeDate, $minuteEndTimeDate);

            //set endDateTime Day on dateBefore
            $endTimeDateBefore = $AgendaTempBefore->getLetter()->getEndTime();
            $hourStartTimeDateBefore = $endTimeDateBefore->format('H');
            $minuteStartTimeDateBefore = $endTimeDateBefore->format('i');
            $endDateTimeDayBefore = $immutableDay
                ->modify('-1 day')
                ->setTime($hourStartTimeDateBefore, $minuteStartTimeDateBefore);

            //set StartDateTime Day on dateAfter
            $startTimeDateAfter = $AgendaTempAfter->getLetter()->getStartTime();
            $hourStartTimeDateAfter = $startTimeDateAfter->format('H');
            $minuteStartTimeDateAfter = $startTimeDateAfter->format('i');
            $startDateTimeDayAfter = $immutableDay
                ->modify('+1 day')
                ->setTime($hourStartTimeDateAfter, $minuteStartTimeDateAfter);

            //Check intervalBefore and intervalAfter
            $intervalBefore = $endDateTimeDayBefore->diff($startDateTimeDay)->format('%H:%I:%S');
            $intervalAfter = $endDateTimeDay->diff($startDateTimeDayAfter)->format('%H:%I:%S');

            $interval = [
                $intervalBefore,
                $intervalAfter,
                $startDateTimeDay,
                $endDateTimeDay
            ];

            return $interval;
        }

    }

}


