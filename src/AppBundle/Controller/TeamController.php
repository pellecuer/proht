<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


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
     * @Route("/Create", name="createAgents")
     * @Method({"GET", "POST"})
     */
    public function createAction()
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
     * @Route("/{id}/edit", name="editTeam")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Team $team)
    {
        $deleteForm = $this->createDeleteForm($planeModel);
        $editForm = $this->createForm('AppBundle\Form\PlaneModelType', $planeModel);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('planemodel_edit', array('id' => $planeModel->getId()));
        }
        return $this->render('planemodel/edit.html.twig', array(
            'planeModel' => $planeModel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        )); 
    }     
}
