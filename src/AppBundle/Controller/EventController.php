<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Event;
use AppBundle\Form\Type\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


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
        //get objects from entity    
        $events = $this->getDoctrine()->getRepository(Event::class)->findAll();              

        if (!$events) {
            throw $this->createNotFoundException(
                'No event found'
            );
        }        
        return $this->render('event/show.html.twig', array(
                'events' => $events,                
            ));
    }
    
    /**
     * @Route("/create", name="createEvent")
     */
    public function CreateAction(Request $request)
    {        
        // 1) build the form
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            $data = $form->getData();           
                       
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);            
            $entityManager->flush();

            //return new Response('Saved new event with id '.$event->getId());
            $this->addFlash('success',
                    'Nouvel évènement crée avec l\'id :' . $event->getId()
            );
            return $this->redirectToRoute('showEvent');

        }else {
            // now - 200 days
            $startDate = new \DateTime('now',  new \DateTimeZone('Europe/Paris'));
            $endDate = new \DateTime(('now + 27 days'), new \DateTimeZone('Europe/Paris'));
            //dump($endDate);die;            
        }
        return $this->render('event/create.html.twig', array(
            'startDate' => $startDate,
            'endDate' => $endDate,
            'form' => $form->createView(),
        ));         
    }
    
    /**     
     * @Route("/{id}/edit", name="editEvent")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
        //Check the object to update in database
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($id);        
        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for id '.$id
            );
        }
        
        //check datasForm
        $form = $this->createForm('AppBundle\Form\Type\EventType', $event);
        $form->handleRequest($request);        
        
        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            
            $event->setAgent('Michel');
            $event->setletter('Z');
            $event->setDate($date);
                       
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);            
            $entityManager->flush(); 
        }
}
