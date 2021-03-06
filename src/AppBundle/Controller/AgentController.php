<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Agent;
use AppBundle\Form\Type\AgentType;
use AppBundle\Form\Type\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Agent controller.
 *
 * @Route("agent")
 */
class AgentController extends Controller
{
    
    /**
    * @Route("/register", name="user_registration")    
    */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        //Check Role        
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas enregistrer un agent. Vous devez être Administrateur pour çelà.'
                );
               return $this->redirectToRoute('showagent'); 
            } 
        
        // 1) build the form
        $agent = new Agent();
        $form = $this->createForm(RegistrationType::class, $agent);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($agent, $agent->getPlainPassword());
            $agent->setPassword($password);

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($agent);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('showagent');
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
    
    
     /**
        * @Route("/show/{id}", name="findAgent")
     */
    public function findAgentAction($id)
    {        
        $agent = $this->getDoctrine()->getRepository(Agent::class)
                ->find($id); 
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
        //dump($agents[0]->getRoles());die;
        $agents = $this->getDoctrine()
                ->getRepository(Agent::class)
                ->findAll();       
         
        
        return $this->render('agent/show.html.twig', array(
                'agents' => $agents,
            ));
    }    
    
    
    /**
     * Displays a form to edit an existing agent entity.
     *
     * @Route("/{id}/edit", name="editAgent")     
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Agent $agent, UserPasswordEncoderInterface $passwordEncoder)    
    {        
        //Check Role        
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas éditer un agent. Vous devez être Administrateur pour çelà.'
                );
               return $this->redirectToRoute('showagent'); 
            } 
            
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            //Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($agent, $agent->getPlainPassword());
            $agent->setPassword($password);
            
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                    'L\'agent avec le nom ' . $agent->getName(). ' a été modifié avec succès'
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
        //Check Role        
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas supprimer un agent. Vous devez être Administrateur pour çelà.'
                );
               return $this->redirectToRoute('showagent'); 
            } 
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($agent);
        $em->flush();
        $this->addFlash('success', 'L\'agent ' . $agent->getName() .  ' a bien été supprimé');
           
        return $this->redirectToRoute('showagent');
    } 
}
