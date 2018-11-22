<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Section;
use AppBundle\Form\Type\SectionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Section controller.
 *
 * @Route("section")
 */
class SectionController extends Controller
{
    /**
     * Lists all section entities.
     *
     * @Route("/show", name="section_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sections = $em->getRepository('AppBundle:Section')->findAll();

        return $this->render('section/show.html.twig', array(
            'sections' => $sections,
        ));
    }


    /**
     * Displays a form to edit an existing section entity.
     *
     * @Route("/{id}/edit", name="editSection")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Section $section)    
    {        
        $form = $this->createForm(sectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                    'Le section avec l\'id :' . $section->getId(). 'a été modifié avec succès'
            );

            return $this->redirectToRoute('section_index', array('id' => $section->getId()));
            
        }

        return $this->render('section/edit.html.twig', array(
            'section' => $section,
            'form' => $form->createView(),            
        ));
    }
    
    /**
     * Creates a new section entity.
     *
     * @Route("/create", name="create_section")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $section = new Section();
        $form = $this->createForm(sectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($section);
            $em->flush();
            $this->addFlash('success',
                    'Nouveau section crée avec l\'id :' . $section->getId()
            );

            return $this->redirectToRoute('section_index', array('id' => $section->getId()));
        }

        return $this->render('section/create.html.twig', array(
            'section' => $section,
            'form' => $form->createView(),
        ));
    }
        
       /**
     * Deletes a Section entity.
     *
     * @Route("/delete/{id}", name="deleteSection")
     * @Method("GET")
     */
    public function deleteAction(Section $section)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($section);
            $em->flush();
            $this->addFlash('success', 'Le section ' . $section->getName() .  'a bien été supprimé');
        return $this->redirectToRoute('section_index');
    }    
}
