<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Agent;
use AppBundle\Form\Type\AgentType;
use AppBundle\Form\Type\TeamType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Team;
use AppBundle\Entity\Role;
use AppBundle\Entity\Event;





/**
 * Agent controller.
 *
 * @Route("agent")
 */
class AgentController extends Controller
{
    
    /**
        * @Route("/show/{id}", name="findAgent")
     */
    public function findAgentAction($id)
    {
             // finds *all* products 
        $agent = $this->getDoctrine()->getRepository(Agent::class)->find($id);                  

        if (!$agent) {
            throw $this->createNotFoundException(
                'No agent found'
            );
        }        
        
        return $this->render('agent/showOne.html.twig', array(
                'agent' => $agent,
            ));
    }
    
    
    /**
     * @Route("/show", name="showagent")
     */
    public function showAction()
    {
             // finds *all* products 
        $agents = $this->getDoctrine()->getRepository(Agent::class)->findAll();                  

        if (!$agents) {
            throw $this->createNotFoundException(
                'No agent found'
            );
        }        
        
        return $this->render('agent/show.html.twig', array(
                'agents' => $agents,
            ));

        // ... do something, like pass the $product object into a template
    }
    
    /**
     *
     * @Route("/create", name="createAgent")
     */
    public function CreateAction(Request $request)
    {
         // 1) build the form
        $agent = new Agent();
        $form = $this->createForm(AgentType::class, $agent);
               
       // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            $data = $form->getData();           
                       
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);            
            $entityManager->flush();
            
            
            //return new Response('Saved new event with id '.$event->getId());
            $this->addFlash('success',
                    'Nouvel agent crée avec l\'id :' . $agent->getId()
            );  
            
            return $this->redirectToRoute('showagent');
         
        }
        return $this->render('agent/create.html.twig', array(            
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Displays a form to edit an existing agent entity.
     *
     * @Route("/{id}/edit", name="editAgent")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Agent $agent)    
    {        
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                    'L\'agent avec le nom ' . $agent->getName(). 'a été modifié avec succès'
            );

            return $this->redirectToRoute('showagent', array('id' => $agent->getId()));
            
        }

        return $this->render('agent/edit.html.twig', array(
            'agent' => $agent,
            'form' => $form->createView(),            
        ));
    }
    
      /**
     * Deletes an agent entity.
     *
     * @Route("/delete/{id}", name="deleteAgent")
     * @Method("GET")
     */
    public function deleteAction(Agent $agent)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($agent);
            $em->flush();
            $this->addFlash('success', 'L\'agent ' . $agent->getName() .  'a bien été supprimé');
           
        return $this->redirectToRoute('showagent');
    } 
}
