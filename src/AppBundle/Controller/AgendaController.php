<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Agenda;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Form\Type\DateType;

 
class AgendaController extends Controller {
    /**
     * @Route("/agenda", name="agenda")
     */
    public function showAction()
    {
             // finds *all* agenda 
        $agendas = $this->getDoctrine()->getRepository(Agenda::class)->findAll();
                
        
        //ArrayDate();        
        $now = new \DateTime("now");
        $end = new \DateTime("now + 28 days");  
        
        $arrayDates = [];
        $nbjours = 28;
        for($i = 0; $i <= $nbjours; $i++) {
           $arrayDates[] = new \DateTime("now + $i days");  
        }
        
        
        if (!$agendas) {
            throw $this->createNotFoundException(
                'No agenda found'
            );
        }
        
        //LigneDate
        $x = new \DateTime("now-5");
        $y = new \DateTime("now + 28 days");
        $repository = $this->getDoctrine()->getRepository(Agenda::class);
        $db = $repository->createQueryBuilder('p');
        $db
            ->where('p.date > :x')
            ->andwhere('p.date < :y')
            ->setParameter('x', $x)
            ->setParameter('y', $y)
            ->orderBy('p.date', 'ASC')            
            ->setMaxResults(28);

        $dateBeetween = $db->getQuery()->getResult();
        
        //Autre ligne 
        
        //ligne 1
        $name = 'Dupont';
        $agent1 = $repository->findBy(
        ['agent' => $name],
        ['date' => 'ASC']
            );
        
        //Ligne 2
        $name = 'Michel';
        $agent2 = $repository->findBy(
        ['agent' => $name],
        ['date' => 'ASC']
            );
        
        //ligne 3
        $name = 'Durant';
        $agent3 = $repository->findBy(
        ['agent' => $name],
        ['date' => 'ASC']
            );

         
        
        return $this->render('agenda.html.twig', array(
                'agendas' => $agendas,
                    'now' => $now,
                    'end' => $end,
                    'arraydates' => $arrayDates,
                    'agent1' => $agent1,
                    'agent2' => $agent2,
                    'agent3' => $agent3,
                    'db' => $dateBeetween,
                    
            ));
        

        // ... do something, like pass the $product object into a template
    }
    
    /**
     * @Route("/agenda/create", name="createagenda")
     */
    public function CreateAction( )
    {       
        $entityManager = $this->getDoctrine()->getManager();
        $date = new \DateTime('11-01-2018');
        $agenda = new Agenda();        
        $agenda->setAgent('Michel');
        $agenda->setletter('Z');
        $agenda->setDate($date);        

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($agenda);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new agenda with id '.$agenda->getId());
    }
    


    /**
     *  Lists all agenda entities.
     *
     * @Route("/agenda/team", name="agendaTeam")
     * @Method({"GET", "POST"})
     */
    public function indexTeamAction()
    {
        //CreateForm
        /*
        $form = $this->createForm(DateType::class);
        $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $startDate = $data['$startDate'];
            $endDate = $data['$endDate'];
         */
         
         
        $startDate = new \DateTimeImmutable('now - 200 days',  new \DateTimeZone('Europe/Paris'));
        $endDate = new \DateTime(('11-01-2019'));
        $agent = ['Durant', 'Dupont', 'Michel'];
        
        $dateBetweens = $this->getDoctrine()
            ->getRepository(Agenda::class)
            ->findDateBetweenDate($startDate, $endDate);        
        
        
        for ($i = 0; $i < count($agent); $i++) {            
            $agentBetweens[] = $this->getDoctrine()
                ->getRepository(Agenda::class)
                ->findAgentBetweenDate($startDate, $endDate, $agent[$i]);
         }

        return $this->render('agenda.html.twig', [
            'dateBetweens' => $dateBetweens,
            'agentBetweens' => $agentBetweens,
             //'form'=>$form->createView()                
        ]);
        
    }
}