<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        
        if (!$letter) {
            throw $this->createNotFoundException(
                'La lettre saisie ne correspond à aucun code. Veuillez saisir une autre lettre'
            );
            
        } else {
            $id = $request->request->get('id');
            $agendaTemp = $this->getDoctrine()
                    ->getRepository(AgendaTemp::class)
                    ->find($id);
            
            if (!$agendaTemp) {
            throw $this->createNotFoundException(
                'Aucun objet dans la base Agenda ne correspond à votre saisie. Merci de ressayer'
            );
            } else {
            
                $agendaTemp->setLetter($letter);

                $em = $this->getDoctrine()->getManager();
                $em->persist($agendaTemp);
                $em->flush();
                $this->addFlash('success',
                        'Agenda mis à jour pour l\'agent : ' . $agendaTemp->getAgent()->getName()
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

