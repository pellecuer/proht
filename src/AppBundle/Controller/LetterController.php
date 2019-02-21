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
        
        return $this->render('letter/show.html.twig', array(
                'letters' => $letters,
            ));        
    }
    
    /**
     * @Route("/create", name="createLetter")     
     */
    public function CreateAction(Request $request)
    {       
        //Can create Letter if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas créer une Lettre. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('showletter'); 
            } 
        
        $letter = new Letter();
        $form = $this->createForm(LetterType::class, $letter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();         
            $letterToUpperCase = strtoupper($letter->getLetter());            
            $letter->setLetter($letterToUpperCase);
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
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Letter $letter)    
    {        
        //Can edit Letter if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas éditer une Lettre. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('showletter'); 
            } 
        
        $form = $this->createForm(LetterType::class, $letter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $letterToUpperCase = strtoupper($letter->getLetter());            
            $letter->setLetter($letterToUpperCase);
                    
            $em->flush();
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
     * @Method("GET")
     */
    public function deleteAction(Letter $letter)
    {
        //Can delete Letter if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas supprimer une Lettre. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('showletter'); 
            } 
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($letter);
        $em->flush();
        $this->addFlash('success', 'La lettre ' . $letter->getLetter() .  'a bien été supprimée');
        return $this->redirectToRoute('showletter');
    }    
     
}
