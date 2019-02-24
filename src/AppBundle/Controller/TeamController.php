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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



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
        //Can create Team if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas créer une équipe. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('showteam'); 
            } 
        
        
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
         //Can edit Team if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas éditer une équipe. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('showteam'); 
            } 
        
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
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas supprimer une équipe. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('showteam'); 
            } 
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
    public function addAgentAction(Agent $agent)
    {  
        $connectedUser = $this->getUser();
        $team = $connectedUser->getTeam();

        if (!$team){
            $this->addFlash('danger',
                'Vous n\'avez pas d\'équipe. Pour changer un agent d\'équipe, merci de sélectionner un agent et de cliquer sur l\'icone modifier.'
            );
            return $this->redirectToRoute('showagent');
        }


        
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_VALIDEUR')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas ajouter un agent dans votre équipe. Vous devez être valideur pour çelà.'
                );
                return $this->redirectToRoute('showAgents', array('id' => $team->getId()));
            } 
        
        
        $team->addAgent($agent);

        $em = $this->getDoctrine()->getManager();
        $em->persist($agent);
        $em->flush();

        $this->addFlash('success',
                    'L\' agent : ' . $agent->getName(). ' a été ajouté avec succès à la team ' . $team->getName()
            );            
        
       return $this->redirectToRoute('showAgents', array('id' => $team->getId()));
    }
    
     /**
     * Remove an Agent of Team.
     *
     * @Route("/remove/{id}", name="removeAgent")
     * @Method({"GET", "POST"})
     */
    public function removeAgentAction(Agent $agent, Request $request)
    {
        $team = $agent->getTeam();
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_VALIDEUR')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas supprimer un agent de votre équipe. Vous devez être valideur pour çelà.'
                );
               return $this->redirectToRoute('showAgents', array('id' => $team->getId())); 
            } 
        
        //remove Agent
        $team->removeAgent($agent);
        $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
        
        return $this->redirectToRoute('showAgents', array('id' => $team->getId()));
    }    
}
