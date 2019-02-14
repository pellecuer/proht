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
     * @Security("is_granted('ROLE_VALIDEUR')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour créer une équipe; Vous devez avoir le role Valideurs")     
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
     * * @Security("is_granted('ROLE_VALIDEUR')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour éditer une équipe; Vous devez avoir le role Valideur")
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
     * @Security("is_granted('ROLE_VALIDEUR')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour supprimer les agendas; Vous devez avoir le role Valideur")
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
     * @Security("is_granted('ROLE_VALIDEUR')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour ajouter des agents dans une équipe; Vous devez avoir le role Administrateur")
     * @Method({"GET", "POST"})
     */
    public function addAgentAction(Agent $agent)
    {         
        $connectedUser = $this->getUser();        
        $team = $connectedUser->getTeam();
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
     *  @Security("is_granted('ROLE_VALIDEUR')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour supprimer les agents d'une équipe; Vous devez avoir le role Valideur")
     * @Method({"GET", "POST"})
     */
    public function removeAgentAction(Agent $agent, Request $request)
    {         
        //remove Agent
        $team = $agent->getTeam();
        
      
        $team->removeAgent($agent);
        $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();
        
        return $this->redirectToRoute('showAgents', array('id' => $team->getId()));
    }    
}
