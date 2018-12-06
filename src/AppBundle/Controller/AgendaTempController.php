<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Agenda;
use AppBundle\Entity\AgendaTemp;
use AppBundle\Entity\Letter;
use AppBundle\Entity\Agent;
use AppBundle\Entity\Event;
use AppBundle\Entity\Team;
use AppBundle\Entity\Utilisateur;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Team controller.
 *
 * @Route("agendaTemp")
 */
class AgendaTempController extends Controller {
    
    
    /**
     * @Route("/edit", name="/agendaTempEdit")
     */
    public function editAction(Request $request)
    {   
        /* on récupère la lettre envoyée en Ajax */
        $letterUpdate = strtoupper($request->request->get('letter'));
        
        /* on vérifie que la lettre éxiste dans notre base en Ajax */
        $letter = $this
                ->getDoctrine()
                ->getRepository(Letter::class)->findOneBy([
                        'letter' => $letterUpdate
                        ]);
              
        $id = $request->request->get('id');
        $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->find($id);
        
        // si agendaTemp n'éxiste pas, on le crée à partir d'une copie de la team dans agenda
            if (!$agendaTemp) {
                $agenda = $this->getDoctrine()
                        ->getRepository(Agenda::class)
                        ->find($id);
                $agents = $agenda->getAgent()->getTeam()->getAgents();
                
                
                //on récupère les objets agenda pour chaque agent de la team que l'on doit copier à  l'aide du QueryBuilder                                
                $agendas = [];
                for ($i=0; $i<count($agents); $i++){
                    $agendas[] = $this->getDoctrine()
                        ->getRepository(Agenda::class)
                        ->findAgent($agents[$i]);
                    foreach ($agendas[$i] as $agendaToCopy){
                        $em = $this->getDoctrine()->getManager();
                        $agendaTemp = new AgendaTemp();
                        $agendaTemp->setAgent($agendaToCopy->getAgent());
                        $agendaTemp->setLetter($agendaToCopy->getLetter());
                        $agendaTemp->setDate($agendaToCopy->getDate());
                        
                        //A ajouter ultérieurement
                        //$agendaTemp->setUtilisateur($agendaToCopy->getUtilisateur());
                        $em->persist($agendaTemp);
                        $em->flush();
                        }
                }
                $this->addFlash('success',
                        'Agenda mis à jour dans Temp pour l\'agent : ' . $agendaTemp->getAgent()->getName()
                    );
            
            //Sinon si l'agendaTemp éxiste, on fait un update'
            } else {
                $agendaTemp->setLetter($letter);
                $em = $this->getDoctrine()->getManager();
                $em->persist($agendaTemp);
                $em->flush();
                $this->addFlash('success',
                        'Agenda mis à jour dans Temp pour l\'agent : ' . $agendaTemp->getAgent()->getName()
                );
            }
            
                  
        /* la réponse doit être encodée en JSON ou XML, on choisira le JSON
         * la doc de Symfony est bien faite si vous devez renvoyer un objet         *
         */
        $response = new Response(json_encode([
            'titre' => $agendaTemp->getAgent()->getName(),
            'description' => $letterUpdate
            ]));
        
        $response->headers->set('Content-Type', 'application/json');
        return $response;       
    }
    
    
    /**
     * @Route("/edit2/{id}/user/{userId}", name="agendaTempEdit2")
     * @ParamConverter("utilisateur", options={"mapping": {"userId": "id"}})
     * @Method({"GET", "POST"})
     */
    public function edit2Action( Team $team, Utilisateur $utilisateur)
    {          
        //check it temp exist for this User and this Team        
         $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByUserByTeam($team, $utilisateur);         
        
        if (!$agendaTemp) {
                // si l'agendaTemp n'éxiste pas, récupère l'agenda éxistant de la team                
                $agendas = $this->getDoctrine()
                    ->getRepository(Agenda::class)
                    ->findAgendaByTeam($team, $utilisateur);                    
                
                //crée les agendas Temp                
                foreach ($agendas as $agenda){
                        $em = $this->getDoctrine()->getManager();
                        $agendaTemp = new AgendaTemp();
                        $agendaTemp->setAgent($agenda->getAgent());
                        $agendaTemp->setLetter($agenda->getLetter());
                        $agendaTemp->setDate($agenda->getDate());
                        $agendaTemp->setUtilisateur($utilisateur);
                        
                        //A ajouter ultérieurement
                        //$agendaTemp->setUtilisateur($agendaToCopy->getUtilisateur());
                        $em->persist($agendaTemp);
                        $em->flush();
                        }
                        
                return $this->redirectToRoute('showAgendaTemp', array(
                    'id' => $team->getId(),
                    'userId' =>$utilisateur->getId()          
                    ));
        
        } return $this->redirectToRoute('showAgendaTemp', array(
            'id' => $team->getId(),
            'userId' =>$utilisateur->getId() 
                ));      
    }
    
    
    
     /**
     * @Route("/show/{id}/user/{userId}", name="showAgendaTemp")
     * @ParamConverter("utilisateur", options={"mapping": {"userId": "id"}})
     */
    public function showAction( Team $team, Utilisateur $utilisateur)
    {        
        $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findByUtilisateur($utilisateur);         

            if (!$agendaTemp) {
                // si l'agendaTemp n'éxiste pas renvoie la route agenda :
                return $this->redirectToRoute('showAgenda');            
            }  

            // sinon, si l'agendaTemp éxiste déjà pour la team, créer la vue :               

            //build letter Array        
            $agentId = [];        
            $agents = $team->getAgents();
            foreach ($agents as $agent) {
            $agentId[] = $agent->getId();
            }

            $startDate = $team->getEvent()->getStartDate();
            $endDate = $team->getEvent()->getEndDate();  

            $agentBetweens = [];
            For ($i=0; $i<count($agentId); $i++){
                $agentBetweens[] = $this->getDoctrine()
                    ->getRepository(AgendaTemp::class)
                    ->findAllTempBetweenDate($startDate, $endDate, $agentId [$i]);
            }
            //dump($agentBetweens);die;

            $interval = new \DateInterval('P1D');
            $arrayDate = [];
            $immutable = \DateTimeImmutable::createFromMutable($startDate);

            while ($immutable<=$endDate){
                $arrayDate[] =  $immutable;            
                $immutable = $immutable->add($interval);
            }

            return $this->render('agendaTemp.html.twig', [

                'dateBetweens' => $arrayDate,
                'agentBetweens' => $agentBetweens,                
                'team' => $team,
                'startDate' => $startDate,
                'endDate' => $endDate,                                
                 ]);
    }
    
    /**
     * Deletes an agenda entity.
     *
     * @Route("/delete/{id}/user/{userId}", name="deleteTemp")
     * @ParamConverter("utilisateur", options={"mapping": {"userId": "id"}})
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, Team $team, Utilisateur $utilisateur)
    {    
        $agendaToRemoves = $this->getDoctrine()
            ->getRepository(AgendaTemp::class)
            ->findAgendaByTeam($team);
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($agendaToRemoves as $agendaToRemove){
            $em->remove($agendaToRemove);
        }                
        $em->flush();
        $this->addFlash('success', 'L\'agent ' . $agent->getName() .  ' a bien été supprimé de l\'agenda');
           
        return $this->redirectToRoute('showAgendaTemp', array(
            'id' => $team->getId(),
            'userId' =>$utilisateur->getId()          
                    ));
    }
    
    /**
     * Deletes an agenda entity.
     *
     * @Route("/valid/{id}/user/{userId}", name="validTemp")
     * @ParamConverter("utilisateur", options={"mapping": {"userId": "id"}})
     * @Method({"GET", "POST"})
     */
    public function validAction(Request $request, Team $team, Utilisateur $utilisateur)
    {    
        $agendaToUpdates = $this->getDoctrine()
            ->getRepository(AgendaTemp::class)
            ->findTempByUserByTeam($team, $utilisateur);
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ($agendaToUpdates as $agendaToUpdate){
            // update dans l'agenda
            
        }                
        $em->flush();
        $this->addFlash('success', 'L\'agent ' . $agent->getName() .  ' a bien été supprimé de l\'agenda');
           
        return $this->redirectToRoute('showAgendaTemp', array(
                'id' => $team->getId(),
                'userId' =>$utilisateur->getId()          
                    ));               
    } 
}

