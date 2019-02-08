<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Letter;
use AppBundle\Form\Type\LetterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
     *  @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour Créer les lettres; Vous devez avoir le role Administrateur")
     */
    public function CreateAction(Request $request)
    {       
        $letter = new Letter();
        $form = $this->createForm(LetterType::class, $letter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($letter);
            $em->flush();
            $this->addFlash('success',
                    'Nouvelle lettre crée avec l\'id :' . $letter->getId()
            );

            return $this->redirectToRoute('showletter', array('id' => $letter->getId()));
        }

        return $this->render('letter/create.html.twig', array(
            'letter' => $letter,
            'form' => $form->createView(),
        ));
    }
    
    
    /**
     * Displays a form to edit an existing letter entity.
     *
     * @Route("/{id}/edit", name="editLetter")
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour modifier les lettres; Vous devez avoir le role Administrateur")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Letter $letter)    
    {        
        $form = $this->createForm(LetterType::class, $letter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                    'La lettre avec l\'id :' . $letter->getId(). 'a été modifiée avec succès'
            );

            return $this->redirectToRoute('showletter', array('id' => $letter->getId()));
            
        }

        return $this->render('letter/edit.html.twig', array(
            'letter' => $letter,
            'form' => $form->createView(),            
        ));
    }

        
       /**
     * Deletes a Service entity.
     *
     * @Route("/delete/{id}", name="deleteLetter")
     *  @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour supprimer les lettres; Vous devez avoir le role Administrateur")
     * @Method("GET")
     */
    public function deleteAction(Letter $letter)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($letter);
            $em->flush();
            $this->addFlash('success', 'La lettre ' . $letter->getLetter() .  'a bien été supprimée');
        return $this->redirectToRoute('showletter');
    }    
     
}
