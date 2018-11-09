<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Role;
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
        if (!$roles) {
            throw $this->createNotFoundException(
                'No role found'
            );
        }        
        
        return $this->render('role/show.html.twig', array(
                'roles' => $roles,
            ));        
    }
    
    /**     
     * @Route("/{id}/edit", name="editRole")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Role $role)
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
