<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Agenda;
use AppBundle\Entity\Letter;
use AppBundle\Entity\Agent;
use AppBundle\Entity\Event;
use AppBundle\Entity\Team;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
 
class AgendaController extends Controller {
        
    /**
     * @Route("/agendaEdit", name="/agendaEdit")
     */
    public function editAction(Request $request)
    {   
        /* on récupère l'id de l'objet envoyée par la vue */
        $letterUpdate = strtoupper($request->request->get('letter'));        
        $letter = $this->getDoctrine()
                ->getRepository(Letter::class)->findOneBy([
                        'letter' => $letterUpdate
                        ]);
        if (!$letter) {
            throw $this->createNotFoundException(
                'La lettre saisie ne correspond à aucun code. Veuillez saisir une autre lettre'
            );
            
        } else {
            $id = $request->request->get('id');
            $agenda = $this->getDoctrine()
                    ->getRepository(Agenda::class)
                    ->find($id);
            
            if (!$agenda) {
            throw $this->createNotFoundException(
                'Aucun objet dans la base Agenda ne correspond à votre saisie. Merci de ressayer'
            );
            } else {
            
                $agenda->setLetter($letter);

                $em = $this->getDoctrine()->getManager();
                $em->persist($agenda);
                $em->flush();
                $this->addFlash('success',
                        'Agenda mis à jour pour l\'agent : ' . $agenda->getAgent()->getName()
                );
            }
        }
        
        
        switch ($letterUpdate){
            case 'H':
                $titre = 'Repos hebdomadaire';
                $description = '35h minimum minimum';
                break;
            case 'J':
                $titre = 'Journée de travail normal';
                $description = '7h45-16h30';
                break;
            case 'M':
                $titre = 'Horaire M';
                $description = '5h-13h';
                break;
            case 'D':
                $titre = '5h30';
                $description = '15h30';
                break;
            case 'R':
                $titre = 'Repos journalier';
                $description = '11h minimum';
        }
        /* la réponse doit être encodée en JSON ou XML, on choisira le JSON
         * la doc de Symfony est bien faite si vous devez renvoyer un objet         *
         */
        $response = new Response(json_encode(array(
            'titre' => $titre,
            'description' => $description
        )));
        
        $response->headers->set('Content-Type', 'application/json');
        return $response;         
    }


    /**
     *  Lists all agenda entities.
     *
     * @Route("/agenda/show", name="showAgenda")
     * @Method({"GET", "POST"})
     */
    public function indexTeamAction(Request $request)
    {        
        //build the form
        $form = $this->createFormBuilder()
            ->add('startDate', DateType::class, array(
            'placeholder' => 'Choose a delivery option',
            'constraints' => array(
                    new NotBlank()
            ),
            'widget' => 'single_text',
            'label'  => 'Date de début',
            'attr' => array('class' => 'form-group mb-2'),
             ))
                
            ->add('endDate', DateType::class, array(
            'placeholder' => 'Choose a delivery option',
            'constraints' => array(
                    new NotBlank()
            ),
            'widget' => 'single_text',
            'label'  => 'Date de fin',
            'attr' => array('class' => 'form-group mx-sm-3 mb-2'),
            ))
                
             ->add('Team', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'name',
                'attr' => array('class' => 'form-group mx-sm-3 mb-2')  
                ))  
            
            ->add('Envoyer', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary mb-2 sendDate'),
            ))
        
            ->getForm()
            ;
            
        //get date from Form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            $data = $form->getData();
            $startDate = $data['startDate'];            
            $endDate = $data['endDate']; 
            $team = $data['Team'];

        }else {
            // now + 15 days                        
            $team = $this->getDoctrine()
                ->getRepository(Team::class)
                ->find(18);
                } 
            $startDate = $team->getEvent()->getStartDate();
            $endDate = $team->getEvent()->getEndDate();            
            //$endDate = $startDate->add(new \DateInterval('P7D'));
        
        //build letter Array        
        $agentId = [];        
        $agents = $team->getAgents();
        foreach ($agents as $agent) {
        $agentId[] = $agent->getId();
        }
        
        $agentBetweens = [];
        For ($i=0; $i<count($agentId); $i++){
            $agentBetweens[] = $this->getDoctrine()
                ->getRepository(Agenda::class)
                ->findAllBetweenDate($startDate, $endDate, $agentId [$i]);
        }
        //dump($agentBetweens);die;
        
        $interval = new \DateInterval('P1D');
        $arrayDate = [];
        $immutable = \DateTimeImmutable::createFromMutable($startDate);
        
        while ($immutable<=$endDate){
            $arrayDate[] =  $immutable;            
            $immutable = $immutable->add($interval);
        } 
        
        return $this->render('agenda.html.twig', [

            'dateBetweens' => $arrayDate,
            'agentBetweens' => $agentBetweens,            
            'team' => $team,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'form'=>$form->createView()                
             ]);
        }
        
         /**
     * Deletes an agenda entity.
     *
     * @Route("/delete/{agentId}", name="deleteAgenda")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $agentId)    {        
            
        $agent = $this->getDoctrine()
        ->getRepository(Agent::class)                
        ->find($agentId);        
        $startDate = $agent->getTeam()->getEvent()->getStartDate();
        $endDate = $agent->getTeam()->getEvent()->getEndDate();  
             
        $agendaToRemoves = $this->getDoctrine()
            ->getRepository(Agenda::class)
            ->deleteAgentAgendaBetweenDate($startDate, $endDate, $agentId);
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($agendaToRemoves as $agendaToRemove){
            $em->remove($agendaToRemove); 
        }                
        $em->flush();
        $this->addFlash('success', 'L\'agent ' . $agent->getName() .  ' a bien été supprimé de l\'agenda');
           
        return $this->redirectToRoute('showAgenda');
    } 
}