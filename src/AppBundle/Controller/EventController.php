<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Event;
use AppBundle\Form\Type\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


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
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour créer les évènements; Vous devez avoir le role Administrateur")
     */
    public function CreateAction(Request $request)
    {        
        // 1) build the form
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {                       
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
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
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour éditer les évènements; Vous devez avoir le role Administrateur")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(Event::class)->find($id);
        //dump($event);die;
        
        $form = $this->createForm('AppBundle\Form\Type\EventType', $event);
        $form->handleRequest($request);        
         
        if ($form->isSubmitted() && $form->isValid()) {            
            //dump($event->getCode);
            $entityManager->flush($event);
            $this->addFlash('success', 'L\'évènement ' . $event->getCode() .  ' a bien été mis à jour');
            return $this->redirectToRoute('showEvent');
            }
        
        return $this->render('event/edit.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }
    
        /**
     * Deletes an Event entity.
     *
     * @Route("/delete/{id}", name="eventDelete")
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour supprimer les évènements; Vous devez avoir le role Administrateur")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Event $event, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
            $this->addFlash('success', 'L\'évènement ' . $event->getCode() .  'a bien été supprimé');
        return $this->redirectToRoute('showEvent');
    }
}
