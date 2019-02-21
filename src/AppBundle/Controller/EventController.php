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

        
        return $this->render('event/show.html.twig', array(
                'events' => $events,                
            ));
    }
    
    /**
     * @Route("/create", name="createEvent")     
     */
    public function CreateAction(Request $request)
    {        
        //Can create Event if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas créer un évènement. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('showEvent');
            } 

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
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, $id)
    {
        //Can create Event if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas éditer un évènement. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('showEvent');
            } 
            
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
     * @Method("GET")
     */
    public function deleteAction(Request $request, Event $event, $id)
    {
        //Can create Event if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas supprimer un évènement. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('showEvent');
            } 
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();
        $this->addFlash('success', 'L\'évènement ' . $event->getCode() .  'a bien été supprimé');
        return $this->redirectToRoute('showEvent');
    }
}
