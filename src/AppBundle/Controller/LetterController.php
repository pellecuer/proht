<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Letter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Letter controller.
 *
 * @Route("letter")
 */
class LetterController extends Controller {
   
    /**
     * @Route("/show", name="showletter")
     */
    public function showAction()
    {
            
        $letters = $this->getDoctrine()->getRepository(Letter::class)->findAll();
              

        if (!$letters) {
            throw $this->createNotFoundException(
                'No letter found'
            );
        }        
        
        return $this->render('letter/show.html.twig', array(
                'letters' => $letters,
            ));        
    }
    
    /**
     * @Route("/create", name="createLetter")
     */
    public function CreateAction()
    {       
        $entityManager = $this->getDoctrine()->getManager();

        $letter = new Letter();
        $letter->setLetter('J');
        $letter->setTimeRange(8);                      

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($letter);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new agent with id '.$letter->getId());
    }
    
    /**     
     * @Route("/{id}/edit", name="editletter")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Letter $letter)
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
