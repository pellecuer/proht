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
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        //Can create Service if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas créer un service. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('ShowService'); 
            } 
        
        
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
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Service $service)    
    {        
        //Can create Service if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas éditer un service. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('ShowService'); 
            } 
        
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
     * @Method("GET")
     */
    public function deleteAction(Service $service)
    {
        //Can delete Service if has role admin
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $this->addFlash('danger',
                        'Vous ne pouvez pas supprimer un service. Vous devez être administrateur pour çelà.'
                );
                return $this->redirectToRoute('ShowService'); 
            } 
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($service);
        $em->flush();
        $this->addFlash('success', 'Le service ' . $service->getName() .  'a bien été supprimé');
        return $this->redirectToRoute('ShowService');
    }    
}
