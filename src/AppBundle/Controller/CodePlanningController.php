<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\CodePlanning;
use \DateTime;

class CodePlanningController extends Controller
{
    /**
     * @Route("/codeplanning/create", name="createcodeplanning")
     */
    public function CreateAction()
    {       
        $entityManager = $this->getDoctrine()->getManager();
        
        $BeginDate = new DateTime('2018-9-28 12:29:00');
        $EndDate = new DateTime('2018-9-29 12:29:00');
                
        $codePlanning = new CodePlanning();
        $codePlanning->setLetter('A');
        $codePlanning->setTimeRange('120');
        $codePlanning->setBeginDate($BeginDate);
        $codePlanning->setEndDate($EndDate);
        $codePlanning->setEffectiveDuration('8');

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($codePlanning);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new code Planning with id '.$codePlanning->getId());
    }
    
    
    
    
    /**
     * @Route("/codeplanning/show", name="showcodeplanning")
     */
    public function showAction()
    {
             // finds *all* products 
        $codePlannings = $this->getDoctrine()->getRepository(CodePlanning::class)->findAll();
        
                            

        if (!$codePlannings) {
            throw $this->createNotFoundException(
                'No code found'
            );
        }
        
        
        
        return $this->render('codeplanning/show.html.twig', array(
                'codes' => $codePlannings,
            ));

        // ... do something, like pass the $product object into a template
    }
    
}
