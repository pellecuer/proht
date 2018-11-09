<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Agent;
use AppBundle\Entity\Team;
use AppBundle\Entity\Role;


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
    
    /**
     *
     * @Route("/create", name="createAgent")
     */
    public function CreateAction()
    {        
        $agent = new Agent();        
        $agent->setNni('E33466');
        $agent->setFirstName('François');
        $agent->setName('Durand');
        $agent->setFunction("Chef de chantier");
        
        // relates this agent to the team
        $team = $this->getDoctrine()
        ->getRepository(Team::class)
        ->find(2);      
        $agent->setTeam($team);
        
        // relates this agent to the Role
         $role = $this->getDoctrine()
        ->getRepository(Role::class)
        ->find(2);
        $agent->setRole($role);
        
        $entityManager = $this->getDoctrine()->getManager();     
        $entityManager->persist($agent);
        $entityManager->flush();
        return new Response(
            'Saved new agent with id: '.$agent->getId()            
        );
    }
    
    
    
    
    
    
}
