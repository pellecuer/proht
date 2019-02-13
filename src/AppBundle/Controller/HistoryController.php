<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Agent;
use AppBundle\Entity\Agenda;
use AppBundle\Entity\User;
use AppBundle\Entity\HistoryChange;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * history controller.
 *
 * @Route("history")
 */
class HistoryController extends Controller {
   
    /**
     * @Route("/{id}/show", name="showHistory")
     * @Security("is_granted('ROLE_VALIDEUR')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour visualiser l'historique des modifications; Vous devez avoir le role valideur")
     */
    public function showAction(Agent $agent)
    {         
        $historys = $this->getDoctrine()
            ->getRepository(HistoryChange::class)
            ->findByAgent($agent);
        
        if (!$historys) {
            
            $this->addFlash(
            'danger',
            'Il n\'éxiste pas d\'historique pour l\'agent ' . $agent->getName()                    
            );
            return $this->redirectToRoute('showagent'); 
        }        
        
        return $this->render('history/show.html.twig', array(
                'historys' => $historys,
            ));   
    }
}
