<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Agenda;
use AppBundle\Entity\Letter;
use AppBundle\Entity\Agent;
use AppBundle\Entity\Event;
use AppBundle\Entity\Team;


use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\User\UserInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
 
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
                'placeholder' => 'Sélectionner une équipe',
                'attr' => array('class' => 'form-control')  
                ))
                
            ->add('interval', DateIntervalType::class, array(
                'widget' => 'choice',
                'allow_extra_fields' => true,
                'with_years'  => false,
                'with_months' => false,                
                'with_weeks' => true,
                'weeks' => range(1, 3),
                'with_days'   => false,
                'with_hours'  => false,                
                'attr' => array('class' => 'form-control'),                    
                'placeholder' => ['weeks' => 'semaines'],                
             ))
                
            ->add('Envoyer', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-primary sendDate'),
            ))
        
            ->getForm();
        
            
        //get date from Form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            //if role admin ok, else redirect
            $data = $form->getData();
            $dateInterval = $data['interval'];
            $team = $data['Team'];
            $startDate = $team->getEvent()->getStartDate();
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
            $dateInterval = new \DateInterval('P15D');
            $team = $this->getUser()->getTeam();
            
            if ($team) {
                $startDate = $team->getEvent()->getStartDate();
                $agents = $team->getAgents();

            } else {
              $startDate = new \DateTime('now');
              $agents = [];              
            } 
        }

        //build ArrayDate
        $immutable = \DateTimeImmutable::createFromMutable($startDate);        
        $endDate = $immutable->add($dateInterval);
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
        
        return $this->render('agenda.html.twig', [

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
    public function showNextAgendaAction(Request $request, UserInterface $agent, $nextDate, Team $team)
    {    
        //dump ($nextDate);die;
        //build the form
        $form = $this->createFormBuilder()
            ->add('startDate', DateType::class, array(            
            'constraints' => array(
                    new NotBlank()
            ),
            'widget' => 'single_text',
            'label'  => 'Date de début',
            'attr' => array('class' => 'form-control'),
             ))
                
             ->add('Team', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner une équipe',
                'attr' => array('class' => 'form-control')  
                ))
                
            ->add('interval', DateIntervalType::class, array(
                'widget' => 'choice',
                'allow_extra_fields' => true,
                'with_years'  => false,
                'with_months' => false,                
                'with_weeks' => true,
                'weeks' => range(0, 3),
                'with_days'   => false,
                'with_hours'  => false,                
                'attr' => array('class' => 'form-control'),                    
                'placeholder' => ['weeks' => 'semaines'],                
             ))
                
            ->add('Envoyer', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-primary sendDate'),
            ))
        
            ->getForm()
            ;
        
        


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
        
        return $this->render('agenda.html.twig', [

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
     *  show next agendas objects.
     *
     * @Route("/agenda/previous/{previousDate}/team/{team_Id}}", name="showPreviousAgenda")
     * @ParamConverter("team", options={"id": "team_Id"})
     * 
     * @Method({"GET", "POST"})
     */
    public function showPreviousAgendaAction(Request $request, UserInterface $agent, $previousDate, Team $team)
    {    
        //dump ($nextDate);die;
        //build the form
        $form = $this->createFormBuilder()
            ->add('startDate', DateType::class, array(            
            'constraints' => array(
                    new NotBlank()
            ),
            'widget' => 'single_text',
            'label'  => 'Date de début',
            'attr' => array('class' => 'form-control'),
             ))
                
             ->add('Team', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner une équipe',
                'attr' => array('class' => 'form-control')  
                ))
                
            ->add('interval', DateIntervalType::class, array(
                'widget' => 'choice',
                'allow_extra_fields' => true,
                'with_years'  => false,
                'with_months' => false,                
                'with_weeks' => true,
                'weeks' => range(0, 3),
                'with_days'   => false,
                'with_hours'  => false,                
                'attr' => array('class' => 'form-control'),                    
                'placeholder' => ['weeks' => 'semaines'],                
             ))
                
            ->add('Envoyer', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-outline-dark'),
            ))
        
            ->getForm()
            ;
        

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
        
        return $this->render('agenda.html.twig', [

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
    public function deleteAction(Request $request, $agentId)
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
    
}