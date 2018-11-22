<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Team;
use AppBundle\Form\Type\TeamType;
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
    
   
}
