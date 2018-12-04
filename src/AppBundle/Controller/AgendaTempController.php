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
        /* on récupère l'id de l'objet envoyée par la vue */
        $letterUpdate = strtoupper($request->request->get('letter'));
        
        $letter = $this
                ->getDoctrine()
                ->getRepository(Letter::class)->findOneBy([
                        'letter' => $letterUpdate
                        ]);
        
        //on vérifie que la lettre éxiste
        if (!$letter) {
            throw $this->createNotFoundException(
                'La lettre saisie ne correspond à aucun code. Veuillez saisir une autre lettre'
            );
            
        } else {
            // si la lettre éxiste, on récupère l'objet agenda dans temp
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
                
                //on récupère les objets agenda pour chaque agent de la team que l'on doit copier
                $agendas = [];
                $repository = $this->getDoctrine()->getRepository(Agenda::class);
                for ($i=0; $i<count($agents);$i++){
                    $agendas[] = $repository->findOneByBy([
                            'name' => $agents[$i]->getId()
                    ]);                    
                    $em = $this->getDoctrine()->getManager();
                    $agendaTemp = new AgendaTemp();
                    $agendaTemp->setAgent($agendas[$i]->getAgent());
                    $agendaTemp->setLetter($agendas[$i]->getLetter());
                    $agendaTemp->setDate($agendas[$i]->getDate());  
                    $em->persist($agendaTemp);
                    $em->flush();
                    $this->addFlash('success',
                        'Agenda mis à jour dans Temp pour l\'agent : ' . $agendaTemp->getAgent()->getName()
                );                    
                } 
                
                
            } else {          
                //sinon, si l'agendaTemp éxiste, j'update l'éxistant
                $agendaTemp->setLetter($letter);
                $em = $this->getDoctrine()->getManager();
                $em->persist($agendaTemp);
                $em->flush();
                $this->addFlash('success',
                        'Agenda mis à jour dans Temp pour l\'agent : ' . $agendaTemp->getAgent()->getName()
                );               
            }
        }
                       
        /* la réponse doit être encodée en JSON ou XML, on choisira le JSON
         * la doc de Symfony est bien faite si vous devez renvoyer un objet         *
         */
        $response = new Response(json_encode([
            'titre' => 'Voici la réponse',
            'description' => 'héhé'
            ]));
        
        $response->headers->set('Content-Type', 'application/json');
        return $response;       
    }
    
    
     /**
     * @Route("/show/{id}", name="showAgendaTemp")
     */
    public function showAction( Team $team, $id)
    {            
        //Vérifie si l'équipe est constituée
        if (!$team->getAgents()) {
            $this->addFlash('danger',
                    'Equipe pas encore constituée avec l\'id :' . $team->getId()
            );
            return $this->redirectToRoute('showTeam');
        }        
        //Si équipe constituée pour cet utilisateur valideur
        $utilisateur = $this->getDoctrine()
                ->getRepository(Utilisateur::class)
                ->find (1);       
        
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
}

