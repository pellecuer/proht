<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Agenda;
use AppBundle\Entity\Agent;
use AppBundle\Entity\Event;
use AppBundle\Entity\Team;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AppBundle\Service\initializeAgenda;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
 
class AgendaController extends Controller {        
    


    /**
     *  Lists all agenda entities.
     *
     * @Route("/agenda/show", name="showAgenda")
     * @Method({"GET", "POST"})
     */
    public function showAgendaAction(Request $request, UserInterface $agent)
    {
        //build the form
        $form = $this->createFormBuilder()
                
             ->add('Team', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'name',                 
                'placeholder' => 'Choisir une équipe',
                'attr' => array('class' => 'form-control'),
                 'required' => false,
                ))
                
            ->add('Envoyer', SubmitType::class, array(
                'attr' => array('class' => 'btn btn btn-dark btn-lg'),
            ))        
            ->getForm();
        
            
        //get date from Form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            //if role admin ok, else redirect
            $data = $form->getData();            
            $team = $data['Team'];            
            
            $startDate = $team->getEvent()->getStartDate();
            $endDate = $team->getEvent()->getEndDate();
            $agents = $team->getAgents();
            
            //prohibit show team<>myTeam unless granted Admin
            $myTeam =  $this->getUser()->getTeam();
            if ($team != $myTeam && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas voir l\'agenda d\'une autre équipe que la votre'
                );
                return $this->redirectToRoute('showAgenda'); 
            } 
            
        } else {            
            //if form is not submitted, initialize defaults variables            
            $team = $this->getUser()->getTeam(); 
            
            if (!$team){                
                $interval15Day = new \DateInterval('P15D');
                $startDate = new \DateTime('now');
                $immutableStart = \DateTimeImmutable::createFromMutable($startDate);
                $endDate = $immutableStart->add($interval15Day);
                $agents = [];
                
            } else {                
                $startDate = $team->getEvent()->getStartDate();
                $endDate = $team->getEvent()->getEndDate();
                $agents = $team->getAgents(); 
            }  
        }

        //build ArrayDate
        $immutable = \DateTimeImmutable::createFromMutable($startDate); 
        $intervalOneDay = new \DateInterval('P1D');
        $arrayDates = [];
        
        while ($immutable<$endDate){
            $arrayDates[] = $immutable;
            $immutable = $immutable->add($intervalOneDay);
        }

        //build holidays
        $holidays = [];
        foreach ($arrayDates as $arrayDate){
            $holidays[] = $this->getDoctrine()
                ->getRepository(Event::class)
                ->findHolidaysByDate($arrayDate);
        }

        //build agenda        
        $agentBetweens = [];        
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
        
        return $this->render('agenda/agenda.html.twig', [

            'dateBetweens' => $arrayDates,
            'agentBetweens' => $agentBetweens,
            'team' => $team,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'holidays' => $holidays,
            'form'=>$form->createView()                
             ]);
    }
    
    
    
        
    /**
     *  show next agendas objects.
     *
     * @Route("/agenda/next/{nextDate}/team/{team_Id}", name="showNextAgenda")
     * @ParamConverter("team", options={"id": "team_Id"})
     * 
     * @Method({"GET", "POST"})
     */
    public function showNextAgendaAction(UserInterface $agent, $nextDate, Team $team)
    {    
        //dump ($nextDate);die;
        //build the form
        $form = $this->createFormBuilder()
             ->add('Team', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'name',                 
                'placeholder' => 'Choisir une équipe',
                'attr' => array('class' => 'form-control'),
                 'required' => false,
                ))  

            ->add('interval', ChoiceType::class, array(
                'choices' => [
                    'Quinze jours'=> new \DateInterval('P15D'),
                    'Un mois'=> new \DateInterval('P1M'),
                    'Trois semaines'=> new \DateInterval('P3M'),
                ],
                'expanded' => true,
                'multiple' => false,
            ))


                
            ->add('Envoyer', SubmitType::class, array(
                'attr' => array('class' => 'btn btn btn-dark btn-lg'),
            ))
        
            ->getForm();
        
        
        //prohibit show team<>myTeam unless granted Admin
           $myTeam =  $this->getUser()->getTeam();
           if ($team != $myTeam && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
               $this->addFlash('danger',
                       'Vous ne pouvez pas voir l\'agenda d\'une autre équipe que la votre'
               );
               return $this->redirectToRoute('showAgenda'); 
           } 


        //Set Next Date from parameters        
        $immutable = \DateTimeImmutable::createFromMutable(new \DateTime($nextDate));         
        $defaultInterval = new \DateInterval('P15D');
        $endDate = $immutable->add($defaultInterval);
        $agents = $team->getAgents();

        //build ArrayDate
        $interval = new \DateInterval('P1D');
        $arrayDates = [];
        
        while ($immutable<$endDate){
            $arrayDates[] = $immutable;
            $immutable = $immutable->add($interval);
        }

        //build holidays
        $holidays = [];
        foreach ($arrayDates as $arrayDate){
            $holidays[] = $this->getDoctrine()
                ->getRepository(Event::class)
                ->findHolidaysByDate($arrayDate);
        }

        //build agenda
        $agentBetweens = [];        
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
        
        return $this->render('agenda/agenda.html.twig', [

            'dateBetweens' => $arrayDates,
            'agentBetweens' => $agentBetweens,
            'team' => $team,
            'startDate' => $nextDate,
            'endDate' => $endDate,
            'holidays' => $holidays,
            'form'=>$form->createView()                
             ]);
    }
    
    
    /**
     *  show previous agendas objects.
     *
     * @Route("/agenda/previous/{previousDate}/team/{team_Id}}", name="showPreviousAgenda")
     * @ParamConverter("team", options={"id": "team_Id"})
     * 
     * @Method({"GET", "POST"})
     */
    public function showPreviousAgendaAction(UserInterface $agent, $previousDate, Team $team)
    {            
        //build the form
        $form = $this->createFormBuilder()
             ->add('Team', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'name',                 
                'placeholder' => 'Choisir une équipe',
                'attr' => array('class' => 'form-control'),
                 'required' => false,
                ))            

            ->add('interval', ChoiceType::class, array(
                'choices' => [
                    'Quinze jours'=> new \DateInterval('P15D'),
                    'Un mois'=> new \DateInterval('P1M'),
                    'Trois semaines'=> new \DateInterval('P3M'),
                ],
                'expanded' => true,
                'multiple' => false,
            ))


                
            ->add('Envoyer', SubmitType::class, array(
                'attr' => array('class' => 'btn btn btn-dark btn-lg'),
            ))
        
            ->getForm();
        
        
        //prohibit show team<>myTeam unless granted Admin
           $myTeam =  $this->getUser()->getTeam();
           if ($team != $myTeam && !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
               $this->addFlash('danger',
                       'Vous ne pouvez pas voir l\'agenda d\'une autre équipe que la votre'
               );
               return $this->redirectToRoute('showAgenda'); 
           } 
        
        
        //Set previous Date from parameters
        $defaultInterval = new \DateInterval('P15D');
        $end = new \DateTime($previousDate);        
        $start = $end->sub($defaultInterval);
        $immutable = \DateTimeImmutable::createFromMutable($start);
        
        $endDate = $immutable->add($defaultInterval);        
        $agents = $team->getAgents();

        //build ArrayDate
        $interval = new \DateInterval('P1D');
        $arrayDates = [];
        
        while ($immutable<$endDate){
            $arrayDates[] = $immutable;
            $immutable = $immutable->add($interval);
        }  

        //build holidays
        $holidays = [];
        foreach ($arrayDates as $arrayDate){
            $holidays[] = $this->getDoctrine()
                ->getRepository(Event::class)
                ->findHolidaysByDate($arrayDate);
        }

        //build agenda
        $agentBetweens = [];        
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
        
        return $this->render('agenda/agenda.html.twig', [

            'dateBetweens' => $arrayDates,
            'agentBetweens' => $agentBetweens,
            'team' => $team,
            'startDate' => $start,
            'endDate' => $endDate,
            'holidays' => $holidays,
            'form'=>$form->createView()                
             ]);
    }
    
    
    
    
    
    /**
     * Deletes an agenda entity.
     *
     * @Route("/delete/{agentId}", name="deleteAgenda")
     * @Security("is_granted('ROLE_ADMINISTRATEUR')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour supprimer les agendas; Vous devez avoir le role Administrateur")
     * @Method("GET")
     */
    public function deleteAgendaAction($agentId)
    {
            
        $agent = $this->getDoctrine()
        ->getRepository(Agent::class)                
        ->find($agentId);        
        $startDate = $agent->getTeam()->getEvent()->getStartDate();
        $endDate = $agent->getTeam()->getEvent()->getEndDate();  
             
        $agendaToRemoves = $this->getDoctrine()
            ->getRepository(Agenda::class)
            ->deleteAgentAgendaBetweenDate($startDate, $endDate, $agentId);
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($agendaToRemoves as $agendaToRemove){
            $em->remove($agendaToRemove); 
        }                
        $em->flush();
        $this->addFlash('success', 'L\'agent ' . $agent->getName() .  ' a bien été supprimé de l\'agenda');
           
        return $this->redirectToRoute('showAgenda');
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
        
        //Check Roles 
            // if NOT ADMIN & HAS ROLE VALIDEUR : can't initialize agenda of other team
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_VALIDEUR')) {
                if ($this->getUser()->getTeam() != $agent->getTeam()) {
                    $this->addFlash('danger',
                        'Vous ne pouvez pas initialiser l\'agenda d\'un agent d\'une autre équipe que la votre.'
                    );
                    return $this->redirectToRoute('showAgents', array(
                        'id' => $agent->getTeam()->getId(),
                    ));
                }

                // if NOT ADMIN & NOT VALIDEUR : can't initialize agenda
            } else {
                $this->addFlash('danger',
                    'Vous ne pouvez pas initialiser l\'agenda car vous n\'êtes ni valideur, ni administrateur.'
                );
                return $this->redirectToRoute('showAgenda');
            }
        }    
        
        $team = $agent->getTeam();
        $initializeAgenda->initialize($team, $agent);
        $this->addFlash('success', 'L\'agenda a été réinitialisé pour l\'agent ' . $agent->getName());

        return $this->redirectToRoute('showAgents', array(
            'id' => $agent->getTeam()->getId(),
        ));
    }
    
}