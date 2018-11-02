<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Agent;


class AgentController extends Controller
{
    /**
     * @Route("/agent/create", name="createagent")
     */
    public function CreateAction()
    {       
        $entityManager = $this->getDoctrine()->getManager();

        $agent = new Agent();
        $agent->setNNI('E32980');
        $agent->setName('Dupont');
        $agent->setFirstName('Jean');
        $agent->setFunction('rsp Secondaire');
        $agent->setRole('Valideur');
        

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($agent);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new agent with id '.$agent->getId());
    }
    
    
    
    
    /**
     * @Route("/agent/show", name="showagent")
     */
    public function showAction()
    {
             // finds *all* products 
        $agents = $this->getDoctrine()->getRepository(Agent::class)->findAll();
        
        // finds *all* products
        //$agents = $repository->findAll();        
        /*var_dump($agents);die;*/        
        
                    

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
    
}
