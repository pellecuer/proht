<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Agenda;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Form\Extension\Core\Type\DateType;
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
     * @Route("/agenda/team", name="agendaTeam")
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
            //dump($startDate);die;

        }else {
            $startDate = new \DateTimeImmutable('now - 20 days',  new \DateTimeZone('Europe/Paris'));
            $endDate = new \DateTime(('11-01-2019'));
            //dump($endDate);die;
        } 
        
        //build arrayDate
        $startDateArray = $startDate;
        $endDateArray = $endDate;
        $diff=$startDateArray->diff($endDateArray)->days;
        $diff1Day = new \DateInterval('P1D');
        $arrayDate = [];
        for ($i=0;$i<$diff;$i++){
            $arrayDate[] =  $startDateArray;
             $startDateArray = $startDateArray->add($diff1Day);
        }
        //dump($arrayDate);die;
         

        $agent = ['Durant', 'Dupont', 'Michel'];        

        for ($i = 0; $i < count($agent); $i++) {            
            $agentBetweens[] = $this->getDoctrine()
                ->getRepository(Agenda::class)
                ->findAgentBetweenDate($startDate, $endDate, $agent[$i]);
         }

         
        return $this->render('agenda.html.twig', [

       'dateBetweens' => $arrayDate,
        'agentBetweens' => $agentBetweens,
         'form'=>$form->createView()                
        ]);
        }   
        
        
        /*$form = $this->createForm(AgendaType::class);
        $form->handleRequest($request);
       
         if ($form->isSubmitted() && $form->isValid()) {            
            $data = $form->getData();            
            $startDate = $data['$startDate'];            
            $endDate = $data['$endDate'];
            
            return $this->redirectToRoute('filterByDate', array(
                'startDate' => $startDate,
                'endDate' => $endDate,
            ));
         }
         else {
         
         */
             
       
         

              
    
    
    /**
     *  Lists all agenda entities.
     *
     * @Route("/agenda/{startDate}/{endDate}}", name="filterByDate")
     * @Method({"GET", "POST"})
     */
     public function filterByDateAction(Request $request, \DateTime $startDate, \DateTime $endDate)
    {
        $form = $this->createForm(AgendaType::class);
        $form->handleRequest($request);               
        
        //récupérer les placeholder
        
        dump($startDate);
         
        //Créer le formulaire
        //setter la value des dates et team
        //Générer le tableau des dates
        // Générer le tableau des lettres
        // Envoyer la vue
         
     }
     
     
     /**
     *  Lists all agenda entities.
     *
     * @Route("/test", name="test")
     * @Method({"GET", "POST"})
     */
     public function testAction(Request $request)
    {
        $defaultData = array('message' => 'Type your message here');
        $form2 = $this->createFormBuilder($defaultData)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('message', TextareaType::class)
            ->add('send', SubmitType::class)
            ->getForm();
        

        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form2->getData();
           dump($data);die;
        }
         
        
        return $this->render('test.html.twig', [

       
         'form2'=>$form2->createView()
        ]);           
     }
     
}