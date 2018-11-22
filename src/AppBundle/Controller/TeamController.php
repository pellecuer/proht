<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Team;
use AppBundle\Entity\Agent;
use AppBundle\Form\Type\TeamType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Service\initializeAgenda;

/**
 * Team controller.
 *
 * @Route("team")
 */
class TeamController extends Controller {
   
    /**
     * @Route("/show", name="showteam")
     */
    public function showAction()
    {            
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();

        if (!$teams) {
            throw $this->createNotFoundException(
                'No team found'
            );
        }        
        return $this->render('team/show.html.twig', array(
                'teams' => $teams,                
            ));        
    }
    
    /**
     * @Route("/showAgents/{id}", name="showAgents")
     * @Method({"GET", "POST"})
     */
    public function showAgentsAction($id)
    {            
        $team = $this->getDoctrine()    
                ->getRepository(Team::class)
                ->find($id);
        $agents = $team->getAgents();        
        $event = $team->getEvent();
        
        return $this->render('team/showAgents.html.twig', array(
                'agents' => $agents,
                'team' => $team,
                'event' => $event,
            ));
    }
    
    /**
     * Creates a new section entity.
     *
     * @Route("/create", name="create_team")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
            $this->addFlash('success',
                    'Nouvelle équipe crée avec l\'id :' . $team->getId()
            );
            return $this->redirectToRoute('showteam', array('id' => $team->getId()));
        }

        return $this->render('team/create.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Displays a form to edit an existing service entity.
     *
     * @Route("/{id}/edit", name="editTeam")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Team $team)    
    {        
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                    'L\'équipe avec l\'id :' . $team->getId(). 'a été modifié avec succès'
            );

            return $this->redirectToRoute('showteam', array('id' => $team->getId()));
            
        }

        return $this->render('team/edit.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),            
        ));
    }

        
       /**
     * Deletes a Service entity.
     *
     * @Route("/delete/{id}", name="deleteTeam")
     * @Method("GET")
     */
    public function deleteAction(Team $team)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($team);
            $em->flush();
           
        return $this->redirectToRoute('showteam');
    }
    
       /**
     * Add an Agent in Team.
     *
     * @Route("/add/{id}", name="addAgent")
     * @Method({"GET", "POST"})
     */
    public function addAgentAction($id, Request $request, InitializeAgenda $initializeAgenda)
    {
        $team = $this->getDoctrine()    
                    ->getRepository(Team::class)
                    ->find($id);  
        //build the form
        $form = $this->createFormBuilder()
                
            ->add('Agent', EntityType::class, array(
                'class' => Agent::class,
                'choice_label' => 'name',
                'attr' => array('class' => 'form-group mx-sm-3 mb-2')  
                ))  
            
            ->add('Envoyer', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary mb-2 sendDate'),
            ))
        
            ->getForm()
            ;
        
         //get date from Form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $agentArray = $form->getData();             
             $agent = $agentArray['Agent'];            
                  
             //add Agent
            $team->addAgent($agent);

            $em = $this->getDoctrine()->getManager();
            $em->persist($agent);
            $em->flush();

             $this->addFlash('success',
                        'L\' agent : ' . $agent->getName(). ' a été ajouté avec succès à la team' . $team->getName()
                );
            
            //Get the service Initialize 
            $message = $initializeAgenda->getHappyMessage();
            $message = $initializeAgenda->initialize($team, $agent);
            $this->addFlash('success', $message);
            return $this->redirectToRoute('showAgents', array('id' => $team->getId()));        
        }
        
        
        return $this->render('team/addAgent.html.twig', array(
            //'team' => $team,
            'form' => $form->createView(),
            'team' => $team,
            'title'=> 'Ajouter'
            
        ));
    }
    
     /**
     * Remove an Agent of Team.
     *
     * @Route("/remove/{id}", name="removeAgent")
     * @Method({"GET", "POST"})
     */
    public function removeAgentAction($id, Request $request)
    {
        $team = $this->getDoctrine()    
                    ->getRepository(Team::class)
                    ->find($id);
        $agents = $team->getAgents();
        //dump($agents);die;
       
        
        //dump($agent);die;
        //build the form
        $form = $this->createFormBuilder()
                
            ->add('agents', ChoiceType::class, array(                
                'choices' => $agents,            ))
            
            ->add('Envoyer', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-primary mb-2 sendDate'),
            ))
        
            ->getForm()
            ;
        
         //get date from Form
        $form->handleRequest($request);        
        if ($form->isSubmitted() && $form->isValid()) {
             $agentArray = $form->getData();
                      
            $agent = $agentArray['agents'];                                     
                  
             //add Agent
            $team->removeAgent($agent);

            $em = $this->getDoctrine()->getManager();
            $em->persist($agent);
            $em->flush();

             $this->addFlash('success',
                        'L\' agent : ' . $agent->getName(). ' a été supprimé avec succès de la team' . $team->getName()
                );

            return $this->redirectToRoute('showAgents', array('id' => $team->getId()));        
        }
        
        return $this->render('team/removeAgent.html.twig', array(
            //'team' => $team,
            'form' => $form->createView(),
            'team' => $team,
            'title'=> 'Supprimer'
            
        ));
    }    
}
