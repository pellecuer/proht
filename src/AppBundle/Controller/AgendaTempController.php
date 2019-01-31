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
use AppBundle\Service\initializeAgenda;

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
        $agent = $agendaTemp->getAgent();
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

        } else {
            //Define Legal Week and varaibles
            $date = \DateTimeImmutable::createFromMutable($agendaTemp->getDate());
            $startLegalWeek = $checkRules->StartLegalWeek($date);
            $endLegalWeek = $checkRules->EndLegalWeek($date);
            $arrayWeeks = $checkRules->ArrayWeek ($startLegalWeek, $endLegalWeek, $agent, $user);
            $HLetter = $this->getDoctrine()
                ->getRepository(Letter::class)
                ->findOneBy([
                    'letter' =>'H',
                ]);
            $RLetter = $this->getDoctrine()
                ->getRepository(Letter::class)
                ->findOneBy([
                    'letter' =>'R',
                ]);


            //Persist in db for testing
            $agendaTemp->setLetter($letter);
            $em = $this->getDoctrine()->getManager();
            $em->persist($agendaTemp);
            $em->flush();

            //Check if HoursPerWeek is under maximum
            $hoursPerWeek = $checkRules->HoursPerWeek($arrayWeeks);

            
            if ($hoursPerWeek > '48') {
                $errors['Heures hebdomadaires'] = "Le nombre d'heures hebdomadaires dépasse le maximum légal de 48 heures.";
            }

            //check if rest between days is under minimmum legal
            $interval = $checkRules->RestBetweenDays($user, $agent, $agendaTemp);
            $rule = $this->getDoctrine()
                ->getRepository(Rule::class)
                ->find(1);
            $LegalRestBetweenDays =  $rule->getMinRestBetweenDays();
            if ($interval[0] < $LegalRestBetweenDays || $interval[1]< $LegalRestBetweenDays) {
                $errors['repos journalier'] = "Le nombre d'heures de repos minimum entre deux jours est inférieur à 11 heures.";
            }

            //check if H in Legal Week and Legal Week is full
            $H = $checkRules->LookForH($startLegalWeek, $endLegalWeek, $agent, $user, $HLetter);
            
                        
            if (!$H){
                //check if H in Legal Week and Legal Week is full
                if ($checkRules->isLegalWeekFull($startLegalWeek, $endLegalWeek, $agent, $user)) {
                $errors['Repos hebdomadaire H'] = "Il manque un 'H' sur la semaine du " . $startLegalWeek->format('D d M Y'); 
                }
            }

            


            if ($H) {
                // check if R before or after H
                $rBeforeH = $checkRules->RBeforeH($H, $agent, $user, $RLetter);
                $rAfterH = $checkRules->RAfterH($H, $agent, $user, $RLetter);
                $dateH = \DateTimeImmutable::createFromMutable($H[0]->GetDate());

                if (!$rBeforeH && !$rAfterH) {
                    $errors['R around H'] = "Il manque un R avant ou après le H pour la date du : " . $dateH
                            ->modify('-1 day')
                            ->format('D d M Y') ;
                }
            }            

            $nxtH = $checkRules->LookForNextH($endLegalWeek, $agent, $user, $HLetter);

            if ($nxtH) {
                //Check if R before or after next H
                $rBeforeNextH = $checkRules->RBeforeH($nxtH, $agent, $user, $RLetter);
                $rAfterNextH = $checkRules->RAfterH ($nxtH, $agent, $user, $RLetter);
                $dateNextH = $nxtH[0]->GetDate();

                if (!$rBeforeNextH && !$rAfterNextH) {
                    $errors['R around NextH'] = "Il manque un R avant ou après le H pour la date du : " . $dateNextH
                            ->modify('-1 day')                            
                            ->format('D d M Y');
                }
            } 

                

            // check if average of hourPerweek is legal;
            $averageHourPerWeek = $checkRules
                ->averageHourPerWeek($agent, $user, $checkRules, $date, $startLegalWeek);
            $max = $this->getDoctrine()->getRepository(Rule::class)
                ->find(1)->getMaxAveragePerWeek();
            
            /*
            $response = new Response(json_encode([
                'titre' => $averageHourPerWeek,
            ]));
            $response->headers->set('Content-Type', 'application/json');
            return $response;          
            */
            
            
            if ($averageHourPerWeek > $max) {
                $errors['moyenne d\'heures de travail hebdomadaire trop élevé'] = "La moyenne d\'heures de travail hebdomadaire dépasse la durée légale de " . $max . 'sur la semaine du' . $startLegalWeek->format('D d M Y');
            }

            //if $errors, restore $letterInMemory and send $error message
            if (!$errors) {
                $titre = 'Mise à jour Ok';
                $description = 'La lettre ' . $letter->getLetter() . ' a été mise à jour pour l\'agent ' . $agent->getName() . ' à la date du ' . $agendaTemp->getDate()->format('d M Y');

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
            'startDay' => $interval[2]->format('D d M H:i:s'),
            'endDay' => $interval[3]->format('D d M H:i:s'),
            'hoursPerWeek' => $hoursPerWeek,
            'intervalBefore' => $interval[0],
            'intervalAfter' => $interval[1],
            'DateTimeBefore' => $interval[4]->format('D d M H:i:s'),
            'DateTimeAfter' => $interval[5]->format('D d M H:i:s'),
            'average' => $averageHourPerWeek,

        ]));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
     
    /**
     * @Route("/edit2/{id}}", name="agendaTempEdit2")     
     * @Method({"GET", "POST"})
     */
    public function createAction(Agent $agent)
    {          
        //check it temp exist for this User and this Agent        
        $team = $agent->getTeam();
        $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByAgent($agent);
        
        if (!$agendaTemp) {
            // si l'agendaTemp n'éxiste pas, récupère l'agenda éxistant de la team                
            $agendas = $this->getDoctrine()
                ->getRepository(Agenda::class)
                ->findAgendaByTeam($team, $agent);
            
            //crée les agendas Temp                
            foreach ($agendas as $agenda){
                    $em = $this->getDoctrine()->getManager();
                    $agendaTemp = new AgendaTemp();
                    $agendaTemp->setAgent($agenda->getAgent());
                    $agendaTemp->setLetter($agenda->getLetter());
                    $agendaTemp->setDate($agenda->getDate()); 
                    
                    //$agendaTemp->setUtilisateur($agendaToCopy->getUtilisateur());
                    $em->persist($agendaTemp);
                    $em->flush();
                    }

            return $this->redirectToRoute('showOneAgendaTemp', array(
                'id' => $agent->getId(),       
                ));
        
        }  return $this->redirectToRoute('showOneAgendaTemp', array(
            'id' => $agent->getId(), 
                ));          
    }  
    
    
     /**
     * @Route("/show/{id}", name="showAgendaTemp")   
     */
    public function showAction(Agent $agent)
    {        
        $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByAgent($agent);
        
            if (!$agendaTemp) {
                // si l'agendaTemp n'éxiste pas renvoie la route agenda :
                return $this->redirectToRoute('showAgenda');            
            }

            //crée un array d'array des agendas
            $team = $agent->getTeam();            
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
            $agentBetweens = [];
            $agents = $team->getAgents();
            dump($agents[0]);die;
            foreach ($agents as $agent) {
                $agentIdentification = [];
                $agentIdentification[] = [
                    $agent->getId(),
                    $agent->getName(),
                    $agent->getFirstName(),
                    $agent->getNni(),
                    $agent->getFunction()
                    ];

                $agendaDate = [];
                foreach ($arrayDates as $arrayDate) {

                    $agendaDate[] = $this->getDoctrine()
                        ->getRepository(Agenda::class)
                        ->findOneBy([
                            'agent' => $agent,
                            'date' => $arrayDate,
                        ],  ['date' => 'ASC']);                
                }      
                $agentBetweens[] = [$agentIdentification, $agendaDate];            
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
     * @Route("/showOne/{id}", name="showOneAgendaTemp")   
     */
    public function showOneAction(Agent $agent)
    {        
        $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByAgent($agent);
        
            if (!$agendaTemp) {
                // si l'agendaTemp n'éxiste pas renvoie la route agenda :
                return $this->redirectToRoute('showAgenda');            
            }

            //crée un array d'array des agendas
            $team = $agent->getTeam();
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
            $agentBetweens = [];
            $agents = [$agent];
            foreach ($agents as $agent) {
                $agentIdentification = [];
                $agentIdentification[] = [
                    $agent->getId(),
                    $agent->getName(),
                    $agent->getFirstName(),
                    $agent->getNni(),
                    $agent->getFunction()
                    ];

                $agendaDate = [];
                foreach ($arrayDates as $arrayDate) {

                    $agendaDate[] = $this->getDoctrine()
                        ->getRepository(Agenda::class)
                        ->findOneBy([
                            'agent' => $agent,
                            'date' => $arrayDate,
                        ],  ['date' => 'ASC']);                
                }      
                $agentBetweens[] = [$agentIdentification, $agendaDate];            
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
     * @Route("/delete/{id}}", name="deleteTemp")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Agent $agent)
    {    
        $agendaToRemoves = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByAgent($agent);
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($agendaToRemoves as $agendaToRemove){
            $agendaToRemove->setAgent(null);
            $em->remove($agendaToRemove);            
        }                
       $em->flush();
       $this->addFlash('success', 'L\'agenda provisoire a bien été supprimé pour l\'agent ' . $agent->getName());      
           
        return $this->redirectToRoute('showOneAgendaTemp', array(
                'id' => $agent->getId(),                
                    ));
    }
    
    
    
    /**
     * Persist temp in agenda entity.
     *
     * @Route("/valid/{id}", name="validTemp")     
     * @Method({"GET", "POST"})
     */
    public function validAction(Agent $agent, historyAgenda $historyAgenda)
    {    
        $connectedUser = $this->getUser();
        $agendaToUpdates = $this->getDoctrine()
            ->getRepository(AgendaTemp::class)
            ->findTempByAgent($agent);        
        
        $em = $this->getDoctrine()->getManager();
         
        
        foreach ($agendaToUpdates as $agendaToUpdate){
            // update dans l'agenda
            $date = $agendaToUpdate->getDate();
            $letter = $agendaToUpdate->getLetter();
            $agent = $agendaToUpdate->getAgent();            
            
            $agendas = $this->getDoctrine()
            ->getRepository(Agenda::class)
            ->findAgendaToUpdate($date, $agent);            
            
            //Check the differences between new and old version of agenda and persist if difference exists
            foreach ($agendas as $agenda){
                if ($agenda->getLetter() != $letter){
                   $agenda->setLetter($letter);
                    $em->persist($agenda);                    
                    $historyAgenda->history($agenda, $connectedUser);
                }
            }
        }                
        
        $em->flush();
        $this->addFlash('success', 'L\'agenda a bien été mis à jour pour l\'agent ' . $agent->getName());
           
        return $this->redirectToRoute('deleteTemp', array(
                'id' => $agent->getId(),                
                    ));               
    }
    
    /**
     * Initialize an agenda entity.
     *
     * @Route("/initialize/{agentId}", name="InitializeAgenda")
     * @Method("GET")
     */
    public function initializeAction($agentId, InitializeAgenda $initializeAgenda)
    {
        //Get the service Initialize
        $agent = $this->getDoctrine()
        ->getRepository(Agent::class)                
        ->find($agentId);
        $team = $agent->getTeam();        
        $initializeAgenda->initialize($team, $agent); 
        $this->addFlash('success', 'L\'agenda a réinitialisé pour l\'agent ' . $agent->getName());
        
        return $this->redirectToRoute('showAgenda');
    }
}

