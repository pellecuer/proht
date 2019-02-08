<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Service;
use AppBundle\Form\Type\ServiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Service controller.
 *
 * @Route("service")
 */
class ServiceController extends Controller
{
    /**
     * Lists all service entities.
     *
     * @Route("/show", name="ShowService")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $services = $em->getRepository('AppBundle:Service')->findAll();

        return $this->render('service/show.html.twig', array(
            'services' => $services,
        ));
    }

    /**
     * Creates a new service entity.
     *
     * @Route("/create", name="createService")
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour créer les services; Vous devez avoir le role Administrateur")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();
            $this->addFlash('success',
                    'Nouveau service crée avec l\'id :' . $service->getId()
            );

            return $this->redirectToRoute('ShowService', array('id' => $service->getId()));
        }

        return $this->render('service/create.html.twig', array(
            'service' => $service,
            'form' => $form->createView(),
        ));
    }

    

    /**
     * Displays a form to edit an existing service entity.
     *
     * @Route("/{id}/edit", name="editService")
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour éditer les services; Vous devez avoir le role Administrateur")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Service $service)    
    {        
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                    'Le service avec l\'id :' . $service->getId(). 'a été modifié avec succès'
            );

            return $this->redirectToRoute('ShowService', array('id' => $service->getId()));
            
        }

        return $this->render('service/edit.html.twig', array(
            'service' => $service,
            'form' => $form->createView(),            
        ));
    }

        
       /**
     * Deletes a Service entity.
     *
     * @Route("/delete/{id}", name="deleteService")
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour supprimer les services; Vous devez avoir le role Administrateur")
     * @Method("GET")
     */
    public function deleteAction(Service $service)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($service);
            $em->flush();
            $this->addFlash('success', 'Le service ' . $service->getName() .  'a bien été supprimé');
        return $this->redirectToRoute('ShowService');
    }    
}
