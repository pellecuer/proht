<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Agenda;
use AppBundle\Entity\Event;
use AppBundle\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
 
class AgendaController extends Controller {
        
    /**
     * @Route("/agenda/create", name="createagenda")
     */
    public function CreateAction( )
    {       
        $entityManager = $this->getDoctrine()->getManager();
        $date = new \DateTime('11-01-2018');
        $agenda = new Agenda();        
        $agenda->setAgent('Michel');
        $agenda->setletter('Z');
        $agenda->setDate($date);        

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($agenda);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new agenda with id '.$agenda->getId());
    }


    /**
     *  Lists all agenda entities.
     *
     * @Route("/agenda/show", name="agendaTeam")
     * @Method({"GET", "POST"})
     */
    public function indexTeamAction(Request $request)
    {        
        //build the form
        $form = $this->createFormBuilder()
            ->add('startDate', DateType::class, array(
            'placeholder' => 'Choose a delivery option',
            'constraints' => array(
                    new NotBlank()
            ),
            'widget' => 'single_text',
            'label'  => 'Date de début',
            'attr' => array('class' => 'form-group mb-2'),
             ))
                
            ->add('endDate', DateType::class, array(
            'placeholder' => 'Choose a delivery option',
            'constraints' => array(
                    new NotBlank()
            ),
            'widget' => 'single_text',
            'label'  => 'Date de fin',
            'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
            ))
                
             ->add('Team', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'name',
                'attr' => array('class' => 'form-group mx-sm-3 mb-2')  
                ))  
            
            ->add('Envoyer', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary mb-2 sendDate'),
            ))
        
            ->getForm()
            ;
            
        //get date from Form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            $data = $form->getData();
            $startDate = $data['startDate'];          
            $endDate = $data['endDate']; 
            $team = $data['Team'];

        }else {
            // now + 15 days
            $startDate = new \DateTime('now',  new \DateTimeZone('Europe/Paris'));            
            $endDate = new \DateTime(('now + 15 day')); 
            $team = $this->getDoctrine()
                ->getRepository(Team::class)
                ->find(18);
                }
            //dump($team);die;
        
        //build letter Array        
        $agentId = [];        
        $agents = $team->getAgents();
        foreach ($agents as $agent) {
        $agentId[] = $agent->getId();
        }

        //$agentId  = [139, 140, 141];
        $agentBetweens = [];
        For ($i=0; $i<count($agentId); $i++){
            $agentBetweens[] = $this->getDoctrine()
                ->getRepository(Agenda::class)
                ->findAllBetweenDate($startDate, $endDate, $agentId [$i]);
        }
        //dump($agentBetweens);die;
        
        //build date Array        
        //$diff=$startDate->diff($endDate)->format("%a");
        $interval = new \DateInterval('P1D');
        $arrayDate = [];
        $immutable = \DateTimeImmutable::createFromMutable($startDate);
        while ($immutable<$endDate){
            $arrayDate[] =  $immutable;
             $immutable = $immutable->add($interval);
        }
        //dump($arrayDate);die;
        
        //build event Array
        For ($i=0; $i<count($arrayDate); $i++){
            $eventBetweens[] = $this->getDoctrine()
                ->getRepository(Event::class)
                ->findEventBetweenDate($startDate, $endDate, $arrayDate [$i]);
        }
        
         
   
        return $this->render('agenda.html.twig', [

            'dateBetweens' => $arrayDate,
            'agentBetweens' => $agentBetweens,
            'eventBetweens' => $eventBetweens,
            'form'=>$form->createView()                
             ]);
        } 
}