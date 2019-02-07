<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Rule;
use AppBundle\Form\Type\RuleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
     * * @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour créer des règles du travail; Vous devez avoir le role Administrateur")
     * @Route("/create", name="createRule")
     * 
     */
    public function CreateAction(Request $request)
    {       
         $rule = new Rule();
        $form = $this->createForm(RuleType::class, $rule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rule);
            $em->flush();
            $this->addFlash('success',
                    'Nouvelle règle crée avec l\'id :' . $rule->getId()
            );

            return $this->redirectToRoute('showrule', array('id' => $rule->getId()));
        }

        return $this->render('rule/create.html.twig', array(
            'rules' => $rule,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Displays a form to edit an existing Rule entity.
     *
     * @Route("/{id}/edit", name="editRule")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Rule $rule)    
    {        
        $form = $this->createForm(RuleType::class, $rule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                    'Le rule avec l\'id :' . $rule->getId(). 'a été modifié avec succès'
            );

            return $this->redirectToRoute('showrule', array('id' => $rule->getId()));
            
        }

        return $this->render('rule/edit.html.twig', array(
            'rule' => $rule,
            'form' => $form->createView(),            
        ));
    }

        
       /**
     * Deletes a rule entity.
     *
     * @Route("/delete/{id}", name="deleteRule")
     * @Method("GET")
     */
    public function deleteAction(Rule $rule)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($rule);
            $em->flush();
            $this->addFlash('success', 'La règle avec l\'id ' . $rule->getId() .  'a bien été supprimée');
        return $this->redirectToRoute('showrule');
    }    
     
}
