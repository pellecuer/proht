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
     */
    public function showAction(Agent $agent)
    {         
        //Check Role        
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas visualiser l\'historique des modifications d\'un agent. Vous devez être Administrateur pour çelà.'
                );
               return $this->redirectToRoute('showagent');
            } 
        
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
