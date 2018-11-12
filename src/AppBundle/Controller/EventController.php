<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Event controller.
 *
 * @Route("event")
 */
class EventController extends Controller {
   
    /**
     * @Route("/show", name="showEvent")
     */
    public function showAction()
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
            
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    'Vacances scolaires' => null,
                    'Copat' => true,
                    'Rcd' => false,
                    'Arrêt de tranche' => false),
            'placeholder' => 'Choisissez une option',
            'constraints' => array(
                    new NotBlank()
            ),            
            'label'  => 'type',
            'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
            ))    
            //fin
            
            ->add('Envoyer', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary mb-2 sendDate'),
            ))                
        
            ->getForm()
            ;
        
        //get objects from entity    
        $events = $this->getDoctrine()->getRepository(Event::class)->findAll();
              

        if (!$events) {
            throw $this->createNotFoundException(
                'No event found'
            );
        }        
        
        return $this->render('event/show.html.twig', array(
                'events' => $events,
                'form' =>$form->createView()
            ));        
    }
    
    /**
     * @Route("/create", name="createEvent")
     */
    public function CreateAction()
    {       
        //début nouveau controleur        
            
        //get date from Form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            $data = $form->getData();
            $startDate = $data['startDate'];          
            $endDate = $data['endDate'];
            //dump($startDate);die;

        }else {
            // now - 200 days
            $startDate = new \DateTime('01-01-2016',  new \DateTimeZone('Europe/Paris'));
            $endDate = new \DateTime(('02-02-2020'));
            //dump($endDate);die;
        }
        
        
        //début du controlleur
        $entityManager = $this->getDoctrine()->getManager();

        $event = new Event();
        $event->setEventType('Arrêt de tranche');
        $event->setStartDate(new \DateTime('01-07-2018'));
        $event->setEndDate(new \DateTime('31-07-2018') );

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($event);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new event with id '.$event->getId());
    }
    
    /**     
     * @Route("/{id}/edit", name="editletter")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Event $letter)
    {
        $deleteForm = $this->createDeleteForm($planeModel);
        $editForm = $this->createForm('AppBundle\Form\PlaneModelType', $planeModel);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('planemodel_edit', array('id' => $planeModel->getId()));
        }
        return $this->render('planemodel/edit.html.twig', array(
            'planeModel' => $planeModel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        )); 
    }
     
}
