<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Agent;
use AppBundle\Entity\Agenda;
use AppBundle\Entity\User;
use AppBundle\Entity\HistoryChange;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


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
        $historys = $this->getDoctrine()
                ->getRepository(History::class)
                ->findByAgent($agent);
        
        if (!$historys) {
            throw $this->createNotFoundException(
                'No role found'
            );
        }        
        
        return $this->render('history/show.html.twig', array(
                'historys' => $historys,
            ));   
    }
}
