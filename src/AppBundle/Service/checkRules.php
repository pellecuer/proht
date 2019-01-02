<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\AgendaTemp;
use AppBundle\Entity\Letter;
use AppBundle\Entity\Rule;




class checkRules {
    
    public function __construct(EntityManagerInterface $entityManager)
{
    $this->em = $entityManager;
}



    public function StartLegalWeek(\DateTimeImmutable $date)
    {        
        if ($date->format('w') == 0) {
            $startLegalWeek = $date->modify('sunday 00:00');
        }else {
            $startLegalWeek = $date->modify('last sunday 00:00');
        }
        
        return $startLegalWeek;
    }
    
    public function EndLegalWeek(\DateTimeImmutable $date)
    {        
        $endLegalWeek = $date->modify('next sunday 00:00');
        
        return $endLegalWeek;
    }
    
    public function isLegalWeekFull($startLegalWeek, $endLegalWeek, $agent, $user)
    {        
        $AgendaTempStartLegalWeek = $this->em
            ->getRepository(AgendaTemp::class)
            ->findTempByDateByUserByAgent($startLegalWeek, $agent, $user);

        $AgendaTempEndLegalWeek = $this->em
            ->getRepository(AgendaTemp::class)
            ->findTempByDateByUserByAgent($endLegalWeek, $agent, $user);
        
        if ($AgendaTempStartLegalWeek && $AgendaTempEndLegalWeek) {
            return true;
        } else {
            return false;
        }
    }
    
     public function ArrayWeek ($startLegalWeek, $endLegalWeek, $agent, $user)
    {        
       $arrayWeek = $this->em
                    ->getRepository(AgendaTemp::class)
                    ->findAllTempBetweenDateByUser($startLegalWeek, $endLegalWeek, $agent, $user);
       
       return $arrayWeek;
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
    
    
    public function LookForH($startLegalWeek, $endLegalWeek, $agent, $user, $HLetter)
    {
        //Check if H in legal week        
        $H = $this->em
            ->getRepository(AgendaTemp::class)
            ->findTempBetweenDateByUserByAgentByLetter($startLegalWeek, $endLegalWeek, $agent, $user, $HLetter);       
        
        return $H;
    }
    
    public function LookForNextH($endLegalWeek, $agent, $user, $HLetter)
    {
        //Check if H in legal week + 7
        $nextStartLegalWeek = $endLegalWeek;
        $nextEndLegalWeek = $endLegalWeek->modify('next sunday 00:00');

        $nxtH = $this->em
            ->getRepository(AgendaTemp::class)
            ->findTempBetweenDateByUserByAgentByLetter($nextStartLegalWeek, $nextEndLegalWeek, $agent, $user, $HLetter);       
        
        return $nxtH;
    }    
    
     
    
    public function RBeforeH ($H, $agent, $user, $RLetter)
    {
        $date = $H[0]->getDate();
        $dateBeforeH = $date->modify('-1 day');
        $rBefore = $this->em
            ->getRepository(AgendaTemp::class)
            ->findTempByDateByUserByAgentByLetter($dateBeforeH, $agent, $user, $RLetter);

        
        return $rBefore;
    }

    public function RAfterH ($H, $agent, $user, $RLetter)
    {
        $date = $H[0]->getDate();
        $dateAfterH = $date->modify('+1 day');
        $rAfter = $this->em
            ->getRepository(AgendaTemp::class)
            ->findTempByDateByUserByAgentByLetter($dateAfterH, $agent, $user, $RLetter);


        return $rAfter;
    }
    
    


    
    public function AverageHourPerWeek($agent, $user, $checkRules, $date, $startLegalWeek)
    {
        //find date - 12 semaines
        $LegalMaxAveragePerWeek = $this->em
            ->getRepository(Rule::class)
            ->find(1)
            ->getMaxAveragePerWeek();
        $startAverageDate = $checkRules->StartLegalWeek($date)->modify('-12 week');
        $beCareful = [];
        $totalHours = 0;
        $countWeeks = 0;
        $arrayWeeks = 0;

        if (!$startAverageDate) {
             $beCareful['Nombre de semaines'] = "Le nombre de semaine comprise dans l'arrêt de tranche est inférieur à $LegalMaxAveragePerWeek. Impossible de calculer le moyenne";

        } else {
            $newDate = $startAverageDate;
            while ($newDate <= $startLegalWeek) {
               $startLegalWeek =  $checkRules->StartLegalWeek($newDate);
               $endLegalWeek = $checkRules->EndLegalWeek($newDate);
                if ($checkRules->isLegalWeekFull($startLegalWeek, $endLegalWeek, $agent, $user)){
                    $arrayWeeks += $checkRules->ArrayWeek ($startLegalWeek, $endLegalWeek, $agent, $user);
                    $totalHours += $checkRules->HoursPerWeek($arrayWeeks);
                    $countWeeks +=1;
                    $newDate->modify('+ 1 week');
                }
            }
        }
        $averageHoursPerWeek = $totalHours / $countWeeks;
        return $averageHoursPerWeek;
    }
        
        


    public function RestBetweenDays($user, $agent, $agendaTemp)
    {
        //check the dateBefore
        $date = \DateTimeImmutable::createFromMutable($agendaTemp->getDate());
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
            ->findOneBy([
                'letter' =>'H',
                ]);
        $RLetter = $this->em
            ->getRepository(Letter::class)
            ->findOneBy([
                'letter' =>'R',
            ]);



         //check the dateAfter
        $dayAfter = $date->modify('tomorrow');
        $AgendaTempAfter = $this->em
             ->getRepository(AgendaTemp::class)
             ->findOneBy([
                 'agent' => $agent,
                 'date' => $dayAfter,
                 'user' => $user
             ]);


        //Check interval before and after

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
        //$intervalAfter =  \DateInterval::createFromDateString('11 hours')->format('%H:%I:%S');
       // $intervalBefore =  \DateInterval::createFromDateString('11 hours')->format('%H:%I:%S');

        if (!$AgendaTempBefore || $AgendaTempBefore->getLetter()->getLetter() == 'H' || $AgendaTempBefore->getLetter()->getLetter() == 'R')  {
            //$intervalBefore =  new \DateInterval('PT11H');
            $intervalBefore =  \DateInterval::createFromDateString('11 hours')->format('%H:%I:%S');
        }


        //if no DayAfter or if letter before = R Or H, set intervalBefore to minimum legal (11h)
        if (!$AgendaTempAfter || $AgendaTempAfter->getLetter() == $HLetter || $AgendaTempAfter->getLetter()->getLetter() == $RLetter)  {
            //$intervalAfter =  new \DateInterval('PT11H');
            $intervalBefore =  \DateInterval::createFromDateString('11 hours')->format('%H:%I:%S');
        }




        $interval = [
            $intervalBefore,
            $intervalAfter,
            $startDateTimeDay,
            $endDateTimeDay,
            $endDateTimeDayBefore,
            $startDateTimeDayAfter
        ];


         return $interval;

    }


}


