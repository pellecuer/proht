<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Role;
use AppBundle\Form\Type\RoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * Role controller.
 *
 * @Route("role")
 */
class RoleController extends Controller {
   
    /**
     * @Route("/show", name="showrole")
     */
    public function showAction()
    {            
        $roles = $this->getDoctrine()->getRepository(Role::class)->findAll(); 
          
        return $this->render('role/show.html.twig', array(
                'roles' => $roles,
            ));        
    }
    
   /**
     * Creates a new role entity.
     *
     * @Route("/create", name="createRole")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();
            $this->addFlash('success',
                    'Nouveau role crée avec l\'id :' . $role->getId()
            );

            return $this->redirectToRoute('showrole', array('id' => $role->getId()));
        }

        return $this->render('role/create.html.twig', array(
            'roles' => $role,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Displays a form to edit an existing Role entity.
     *
     * @Route("/{id}/edit", name="editRole")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Role $role)    
    {        
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                    'Le role avec l\'id :' . $role->getId(). 'a été modifié avec succès'
            );

            return $this->redirectToRoute('showrole', array('id' => $role->getId()));
            
        }

        return $this->render('role/edit.html.twig', array(
            'role' => $role,
            'form' => $form->createView(),            
        ));
    }

        
       /**
     * Deletes a role entity.
     *
     * @Route("/delete/{id}", name="deleteRole")
     * @Method("GET")
     */
    public function deleteAction(Role $role)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($role);
            $em->flush();
            $this->addFlash('success', 'Le rôle ' . $role->getName() .  'a bien été supprimé');
        return $this->redirectToRoute('showrole');
    }    
     
}
