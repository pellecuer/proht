<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Agent;
use AppBundle\Form\Type\AgentType;
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
}
