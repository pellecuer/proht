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
        $thanksTo = 'Merci de corriger votre saisie';
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
            //if date = sunday, take this sunday
            if ($date->format('w') == 0) {
                $startLegalWeek = $date->modify('sunday 00:00');
                $endLegalWeek = $date->modify('next sunday 00:00');
            //else take last sunday
            } else {
                $startLegalWeek = $date->modify('last sunday 00:00');
                $endLegalWeek = $date->modify('next sunday 00:00');
            }
            //Persist in db for testing
            $agendaTemp->setLetter($letter);
            $em = $this->getDoctrine()->getManager();
            $em->persist($agendaTemp);
            $em->flush();
            //ArrayDays
            $dayBefore = $date->modify('yesterday 00:00');
            $dayAfter = $date->modify('tomorrow 00:00');
            $arrayDays = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findAllTempBetweenDateByUser($dayBefore, $dayAfter, $agendaTemp->getAgent()->getId(), $user);
            //This Date Letter Time
            $startTimeForTheDay = $arrayDays[1]->getLetter()->getStartTime();
            $hStartTimeForTheDay = $startTimeForTheDay->format('H');
            $istartTimeForTheDay = $startTimeForTheDay->format('i');
            $endTimeForTheDay = $arrayDays[1]->getLetter()->getEndTime();
            $hEndTimeForTheDay = $endTimeForTheDay->format('H');
            $iEndTimeForTheDay = $endTimeForTheDay->format('i');
            $dateTimeStarForTheDay = $date->setTime($hStartTimeForTheDay, $istartTimeForTheDay);
            $dateTimeEndForTheDay = $date->setTime($hEndTimeForTheDay, $iEndTimeForTheDay);
            //Check if HoursPerWeek is under maximum
            $arrayWeeks = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findAllTempBetweenDateByUser($startLegalWeek, $endLegalWeek, $agendaTemp->getAgent()->getId(), $user);
            $hoursPerWeek = $checkRules->HoursPerWeek($agendaTemp, $user, $startLegalWeek, $endLegalWeek, $arrayWeeks);
            if ($hoursPerWeek > '48') {
                $errors['Heures hebdomadaires'] = "Le nombre d'heures hebdomadaires dépasse le maximum légal de 48 heures.";
            }
            //check rest between days
            $interval = $checkRules->restBetweenDays($agendaTemp, $user, $date, $letter, $arrayDays);
            if (!$interval) {
                $errors['repos journalier'] = "Le nombre d'heures de repos minimum entre deux jours est inférieur à 11 heures.";
            }
            // check if  one H in the legal week
            $hLetter = $this->getDoctrine()
                ->getRepository(Letter::class)
                ->findByLetter('H');
            $rLetter = $this->getDoctrine()
                ->getRepository(Letter::class)
                ->findByLetter('R');
            $h = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempBetweenDateByUserByAgentByLetter($startLegalWeek, $endLegalWeek, $agendaTemp->getAgent(), $user, $hLetter);
            if (!$h) {
                $errors['Repos hebdomadaire H'] = "Il manque un 'H' sur la semaine du " . $startLegalWeek->format(('D d M Y'));
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
            ]));
        
        $response->headers->set('Content-Type', 'application/json');
        return $response;
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
                   
                                       
            //$agentBetweens = $this->getDoctrine()
               // ->getRepository(AgendaTemp::class)
               // ->findTempByUserByTeam($team, $user);
            //dump($agentBetweens);die;
            
            //crée un array des agents de la team
            $agentId = [];    
            $agents = $team->getAgents();
            foreach ($agents as $agent) {
                $agentId[] = $agent->getId();
            }
            
            $startDate = $team->getEvent()->getStartDate();
            $endDate = $team->getEvent()->getEndDate();
            
            //crée un array d'array des agendas
            $agentBetweens = [];
            For ($i=0; $i<count($agentId); $i++){
                $agentBetweens[] = $this->getDoctrine()
                    ->getRepository(AgendaTemp::class)
                    ->findAllTempBetweenDateByUser($startDate, $endDate, $agentId [$i], $user);
            }
            //dump ($agentBetweens);die;
            

            $interval = new \DateInterval('P1D');
            $arrayDate = [];
            $immutable = \DateTimeImmutable::createFromMutable($startDate);

            while ($immutable<=$endDate){
                $arrayDate[] =  $immutable;            
                $immutable = $immutable->add($interval);
            }

            return $this->render('agendaTemp.html.twig', [

                'dateBetweens' => $arrayDate,
                'agentBetweens' => $agentBetweens,                
                'team' => $team,
                'startDate' => $startDate,
                'endDate' => $endDate,                                
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

