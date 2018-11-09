<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Rule;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rule controller.
 *
 * @Route("rule")
 */
class RuleController extends Controller {
   
    /**
     * @Route("/show", name="showrule")
     */
    public function showAction()
    {
            
        $rules = $this->getDoctrine()->getRepository(Rule::class)->findAll();
              

        if (!$rules) {
            throw $this->createNotFoundException(
                'No rule found'
            );
        }        
        
        return $this->render('rule/show.html.twig', array(
                'rules' => $rules,
            ));        
    }
    
    /**
     * @Route("/create", name="createRule")
     */
    public function CreateAction()
    {       
        $entityManager = $this->getDoctrine()->getManager();

        $rule = new Rule();
        $rule->setMaxHourPerDay(10);
        $rule->setMinRestPerWeek(24);
        $rule->setMinRestBetweenDays(11);
        $rule->setMaxHourPerWeek(48);
        $rule->setMaxAveragePerWeek(44);
        $rule->setNbWeekForAverage(12);                    

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($rule);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new agent with id '.$rule->getId());
    }
    
    /**     
     * @Route("/{id}/edit", name="editrule")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Rule $rule)
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
