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
                'attr' => array('class' => 'form-control')  
                ))
                
            ->add('interval', DateIntervalType::class, array(
                'widget' => 'choice',
                'allow_extra_fields' => true,
                'with_years'  => false,
                'with_months' => false,
                'with_weeks' => true,
                'weeks' => range(0, 4),
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
        
        //initialize defaults variables
        $team = $this->getUser()->getTeam();
        $startDate = $team->getEvent()->getStartDate();        
        //$startDate = new \DateTime('now');
        $immutable = \DateTimeImmutable::createFromMutable($startDate);
        $defaultInterval = new \DateInterval('P15D');
        $endDate = $immutable->add($defaultInterval);
        $agents = $team->getAgents();
            
        //get date from Form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            $data = $form->getData();
            $dateInterval = $data['interval'];
            $team = $data['Team'];
            $startDate = $data['startDate'];
            $immutable = \DateTimeImmutable::createFromMutable($startDate);           
            $endDate = $immutable->add($dateInterval);            
        }

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
            'startDate' => $startDate,
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