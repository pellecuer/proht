<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Agenda;
use AppBundle\Entity\AgendaTemp;
use AppBundle\Entity\Letter;
use AppBundle\Entity\Agent;
use AppBundle\Entity\Event;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Entity\Rule;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Service\historyAgenda;
use AppBundle\Service\checkRules;

/**
 * Team controller.
 *
 * @Route("agendaTemp")
 */
class AgendaTempController extends Controller {


    /**
     * @Route("/edit", name="agendaTempEdit")
     */
    public function editAjaxAction(Request $request, UserInterface $user, checkRules $checkRules)
    {

        /*  Get the object agendaTemp send by Ajax */
        $id = $request->request->get('id');
        $agendaTemp = $this->getDoctrine()
            ->getRepository(AgendaTemp::class)
            ->find($id);
        $letterInMemo = $agendaTemp->getLetter();


        /* Get the letter send by Ajax */
        $letterUpdate = strtoupper($request->request->get('letter'));

        /* check if letter exist */
        $letter = $this
            ->getDoctrine()
            ->getRepository(Letter::class)->findOneBy([
                'letter' => $letterUpdate
            ]);

        /* if letter doesn't exist, restore letter*/
        $errors = [];
        $thanksTo = 'Merci de refaire votre saisie sur la date du ' . $agendaTemp->getDate()->format ('D d M Y');
        if (!$letter) {
            $errors['Lettre'] = "La lettre ' $letterUpdate ' n’éxiste pas dans le code planning. " . $thanksTo;
            $response = new Response(json_encode(array(

                'letter' => $letterInMemo->getLetter(),
                'bgLetter' => '',
                'titre' => 'Erreur :',
                'description' =>  $errors['Lettre']
            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        }
        /* if letter exist */
        else {
            //Define Legal Week
            $date = \DateTimeImmutable::createFromMutable($agendaTemp->getDate());
            $startLegalWeek = $checkRules->StartLegalWeek($date);
            $endLegalWeek = $checkRules->SendLegalWeek($date);


            //Persist in db for testing
            $agendaTemp->setLetter($letter);
            $em = $this->getDoctrine()->getManager();
            $em->persist($agendaTemp);
            $em->flush();


            //define agendaTempAround as an array of agendaTemp objects around agendaTemp
            $dayBefore = $date->modify('yesterday 00:00');
            $dayAfter = $date->modify('tomorrow 00:00');
            $agendaTempAround = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findAllTempBetweenDateByUser($dayBefore, $dayAfter, $agendaTemp->getAgent()->getId(), $user);

            //This Date Letter Time
            $startTimeForTheDay = $agendaTempAround[1]->getLetter()->getStartTime();
            $endTimeForTheDay = $agendaTempAround[1]->getLetter()->getEndTime();
            $dateTimeStarForTheDay = $date->setTime($startTimeForTheDay->format('H'), $startTimeForTheDay->format('i'));
            $dateTimeEndForTheDay = $date->setTime($endTimeForTheDay->format('H'), $endTimeForTheDay->format('i'));

            //Check if HoursPerWeek is under maximum
            $arrayWeeks = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findAllTempBetweenDateByUser($startLegalWeek, $endLegalWeek, $agendaTemp->getAgent(), $user);

            $hoursPerWeek = $checkRules->HoursPerWeek($agendaTemp, $user, $startLegalWeek, $endLegalWeek, $arrayWeeks);
            if ($hoursPerWeek > '48') {
                $errors['Heures hebdomadaires'] = "Le nombre d'heures hebdomadaires dépasse le maximum légal de 48 heures.";
            }

            //check rest between days
            $interval = $checkRules->restBetweenDays($agendaTemp, $user, $date, $letter, $agendaTempAround);
            if ($interval[0]<11 || $interval[1] < 11) {
                $errors['repos journalier'] = "Le nombre d'heures de repos minimum entre deux jours est inférieur à 11 heures.";
            }


            $hLetter = $this->getDoctrine()
                ->getRepository(Letter::class)
                ->findByLetter('H');
            $rLetter = $this->getDoctrine()
                ->getRepository(Letter::class)
                ->findByLetter('R');
            $h = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempBetweenDateByUserByAgentByLetter($startLegalWeek, $endLegalWeek, $agendaTemp->getAgent(), $user, $hLetter);

            //Error msg if no H and $startLegalweek or $endLegalWeek out of range
            $AgendaTempStartLegalWeek = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByDateByUserByAgent($startLegalWeek, $agendaTemp->getAgent(), $user);

            $AgendaTempEndLegalWeek = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByDateByUserByAgent($endLegalWeek, $agendaTemp->getAgent(), $user);

            if (!$h) {
                if ($AgendaTempStartLegalWeek && $AgendaTempEndLegalWeek){
                    $errors['Repos hebdomadaire H'] = "Il manque un 'H' sur la semaine du " . $startLegalWeek->format('D d M Y');
                }
            }
            // check if  one H with R around
            $rBefore = '';
            $rAfter = '';
            if ($h) {
                $dateBeforeH = \DateTimeImmutable::createFromMutable($h[0]->getDate()->modify('-1 day'));
                $dateAfterH = \DateTimeImmutable::createFromMutable($h[0]->getDate()->modify('+2 day'));
                $rBefore = $this->getDoctrine()
                    ->getRepository(AgendaTemp::class)
                    ->findTempByDateByUserByAgentByLetter($dateBeforeH, $agendaTemp->getAgent(), $user, $rLetter);
                $rAfter = $this->getDoctrine()
                    ->getRepository(AgendaTemp::class)
                    ->findTempByDateByUserByAgentByLetter($dateAfterH, $agendaTemp->getAgent(), $user, $rLetter);

                if (!$rBefore && !$rAfter)  {
                    $errors['Repos hebdomadaire R'] = "Il manque un R avant ou après le H pour la date : " . $dateBeforeH->format('D d M Y') . ' ou ' . $dateAfterH->format('D d M Y');
                }
            }

            // check if average of hourPerweek is legal
            $averageHourPerWeek = $checkRules->averageHourPerWeek($agendaTemp, $checkRules, $user, $arrayWeeks);
            $max = $this->getDoctrine()->getRepository(Rule::class)
                ->find(1)->getMaxAveragePerWeek();
            if ($averageHourPerWeek > $max) {
                $errors['moyenne d\'heures de travail hebdomadaire trop élevé'] = "La moyenne d\'heures de travail hebdomadaire dépasse la durée légale de " . $max . 'sur la semaine du' . $startLegalWeek->format('D d M Y');
            }





            //if $errors, restore $letterInMemory and send $error message
            if (!$errors) {
                $titre = 'Mise à jour Ok';
                $description = 'La lettre ' . $letter->getLetter() . ' a été mise à jour pour l\'agent ' . $agendaTemp->getAgent()->getName() . ' à la date du ' . $agendaTemp->getDate()->format('d M Y');

            } else {
                $titre = 'Erreur';
                $description = implode(" ", $errors);
                $agendaTemp->setLetter($letterInMemo);
                $em->persist($agendaTemp);
                $em->flush();
            }


            //Change background if letter change
            $updatedLetter = $agendaTemp->getLetter()->getLetter();
            if ($updatedLetter == 'R') {
                $bgLetter = 'table-success';
            } elseif ($updatedLetter == 'H') {
                $bgLetter = 'table-danger';
            } else {
                $bgLetter = 'table-info';
            }

        }



        $response = new Response(json_encode([
            'titre' => $titre,
            'description' => $description,
            'letter' => $updatedLetter,
            'bgLetter' => $bgLetter,
            'startLegalWeek' => $startLegalWeek->format('D d M Y H:i:s'),
            'endLegalWeek' => $endLegalWeek->format('D d M Y H:i:s'),
            'startDay' => $dateTimeStarForTheDay->format('D d M H:i:s'),
            'endDay' => $dateTimeEndForTheDay->format('D d M H:i:s'),
            'hoursPerWeek' => $hoursPerWeek,
            'intervalBefore' => $interval[0],
            'intervalAfter' => $interval[1],

            'average' => $averageHourPerWeek,

        ]));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
     
    /**
     * @Route("/edit2/{id}/user/{userId}", name="agendaTempEdit2")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     * @Method({"GET", "POST"})
     */
    public function copyAgendaAction( Team $team, User $user)
    {          
        //check it temp exist for this User and this Team        
         $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByUserByTeam($team, $user);
         //dump($agendaTemp);die;
        
        if (!$agendaTemp) {
                // si l'agendaTemp n'éxiste pas, récupère l'agenda éxistant de la team                
                $agendas = $this->getDoctrine()
                    ->getRepository(Agenda::class)
                    ->findAgendaByTeam($team, $user);                
                
                //crée les agendas Temp                
                foreach ($agendas as $agenda){
                        $em = $this->getDoctrine()->getManager();
                        $agendaTemp = new AgendaTemp();
                        $agendaTemp->setAgent($agenda->getAgent());
                        $agendaTemp->setLetter($agenda->getLetter());
                        $agendaTemp->setDate($agenda->getDate());
                        $agendaTemp->setUser($user);
                        
                        //A ajouter ultérieurement
                        //$agendaTemp->setUtilisateur($agendaToCopy->getUtilisateur());
                        $em->persist($agendaTemp);
                        $em->flush();
                        }
                        
                return $this->redirectToRoute('showAgendaTemp', array(
                    'id' => $team->getId(),
                    'userId' =>$user->getId()         
                    ));
        
        }  return $this->redirectToRoute('showAgendaTemp', array(
            'id' => $team->getId(),
            'userId' =>$user->getId()
                ));          
    }  
    
    
     /**
     * @Route("/show/{id}/user/{userId}", name="showAgendaTemp")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     */
    public function showAction( Team $team, User $user)
    {        
        $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByUserByTeam($team, $user);              

            if (!$agendaTemp) {
                // si l'agendaTemp n'éxiste pas renvoie la route agenda :
                return $this->redirectToRoute('showAgenda');            
            }



            //crée un array d'array des agendas
            $startDate = $team->getEvent()->getStartDate();
            $endDate = $team->getEvent()->getEndDate();


            
            //build calendar
            $interval = new \DateInterval('P1D');
            $arrayDates = [];
            $immutable = \DateTimeImmutable::createFromMutable($startDate);

            while ($immutable<=$endDate){
                $arrayDates[] =  $immutable;
                $immutable = $immutable->add($interval);
            }

            //show holidays
            $holidays = [];
            foreach ($arrayDates as $arrayDate){
                $holidays[] = $this->getDoctrine()
                    ->getRepository(Event::class)
                    ->findHolidaysByDate($arrayDate);
            }

            //Showagendas
            $agents = $team->getAgents();
            $agentBetweens = [];
            foreach ($agents as $agent) {
                $agendaDate = [];
                foreach ($arrayDates as $arrayDate) {
                    $agendaDate[] = $this->getDoctrine()
                        ->getRepository(AgendaTemp::class)
                        ->findOneBy([
                            'agent' => $agent,
                            'date' => $arrayDate,
                        ],  ['date' => 'ASC']);
                }
                $agentBetweens[] = $agendaDate;
            }






        return $this->render('agendaTemp.html.twig', [

                'dateBetweens' => $arrayDates,
                'agentBetweens' => $agentBetweens,
                'team' => $team,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'holidays' => $holidays,
                 ]);
    }
    
    /**
     * Deletes an agenda entity.
     *
     * @Route("/delete/{id}/user/{userId}", name="deleteTemp")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, Team $team, User $user)
    {    
        $agendaToRemoves = $this->getDoctrine()
            ->getRepository(AgendaTemp::class)
            ->findTempByUserByTeam($team, $user);
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($agendaToRemoves as $agendaToRemove){
            $agendaToRemove->setAgent(null);
            $em->remove($agendaToRemove);            
        }                
        $em->flush();
       $this->addFlash('success', 'L\'agenda provisoire a bien été supprimé pour l\'équipe ' . $team->getName());
      
           
        return $this->redirectToRoute('showAgenda');
    }
    
    
    
    /**
     * Persist temp in agenda entity.
     *
     * @Route("/valid/{id}/user/{userId}", name="validTemp")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     * @Method({"GET", "POST"})
     */
    public function validAction(Request $request, Team $team, User $user, historyAgenda $historyAgenda)
    {    
        $agendaToUpdates = $this->getDoctrine()
            ->getRepository(AgendaTemp::class)
            ->findTempByUserByTeam($team, $user);
        //dump($agendaToUpdates);die;
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($agendaToUpdates as $agendaToUpdate){
            // update dans l'agenda
            $date = $agendaToUpdate->getDate();
            $letter = $agendaToUpdate->getLetter();
            $agent = $agendaToUpdate->getAgent();            
            
            $agendas = $this->getDoctrine()
            ->getRepository(Agenda::class)
            ->findAgendaToUpdate($date, $agent);
            
            foreach ($agendas as $agenda){
                if ($agenda->getLetter() != $letter){
                   $agenda->setLetter($letter);
                    $em->persist($agenda);
                    $historyAgenda->history($agenda, $user); 
                }
            }
        }                
        
        $em->flush();
        $this->addFlash('success', 'L\'agenda a bien été mis à jour pour l\'équipe ' . $team->getName());
           
        return $this->redirectToRoute('deleteTemp', array(
                'id' => $team->getId(),
                'userId' =>$user->getId()
                    ));               
    }
}

