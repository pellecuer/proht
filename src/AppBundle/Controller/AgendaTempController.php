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
use AppBundle\Entity\User;

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
    public function editAjaxAction(Request $request)
    {   
        /* on récupère la lettre envoyée en Ajax */
        $letterUpdate = strtoupper($request->request->get('letter'));
        
        /* on vérifie que la lettre éxiste dans notre base en Ajax */
        $letter = $this
                ->getDoctrine()
                ->getRepository(Letter::class)->findOneBy([
                        'letter' => $letterUpdate
                        ]);
        
         if (!$letter) {
                // si la lettre n'éxiste pas renvoie la route agendaTempEdit
                $response = new Response(json_encode(array(
                'titre' => 'Erreur :',
                'description' => 'La lettre saisie n\'éxiste pas. Veuillez refaire votre saisie'
                )));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
        }                                
             
         // si agendaTemp n'éxiste pas, on le crée à partir d'une copie de la team dans agenda     
        $id = $request->request->get('id');
        $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->find($id);   
            
        $agendaTemp->setLetter($letter);
        $em = $this->getDoctrine()->getManager();
        $em->persist($agendaTemp);
        $em->flush();
        
        $response = new Response(json_encode([
            'titre' => 'Mise à jour Ok',
            'description' => 'La lettre ' . $letter->getLetter() . ' a été mise à jour pour l\'agent ' . $agendaTemp->getAgent()->getName() . ' à la date du ' . $agendaTemp->getDate()->format('d M Y')
            ]));
        
        $response->headers->set('Content-Type', 'application/json');
        return $response;       
    }
    
    
    /**
     * @Route("/edit2/{id}/user/{userId}", name="agendaTempEdit2")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     * @Method({"GET", "POST"})
     */
    public function copyAgendaAction( Team $team, User $user)
    {          
        //check it temp exist for this User and this Team        
         $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByUserByTeam($team, $user);
         //dump($agendaTemp);die;
        
        if (!$agendaTemp) {
                // si l'agendaTemp n'éxiste pas, récupère l'agenda éxistant de la team                
                $agendas = $this->getDoctrine()
                    ->getRepository(Agenda::class)
                    ->findAgendaByTeam($team, $user);                
                
                //crée les agendas Temp                
                foreach ($agendas as $agenda){
                        $em = $this->getDoctrine()->getManager();
                        $agendaTemp = new AgendaTemp();
                        $agendaTemp->setAgent($agenda->getAgent());
                        $agendaTemp->setLetter($agenda->getLetter());
                        $agendaTemp->setDate($agenda->getDate());
                        $agendaTemp->setUser($user);
                        
                        //A ajouter ultérieurement
                        //$agendaTemp->setUtilisateur($agendaToCopy->getUtilisateur());
                        $em->persist($agendaTemp);
                        $em->flush();
                        }
                        
                return $this->redirectToRoute('showAgendaTemp', array(
                    'id' => $team->getId(),
                    'userId' =>$user->getId()         
                    ));
        
        }  return $this->redirectToRoute('showAgendaTemp', array(
            'id' => $team->getId(),
            'userId' =>$user->getId()
                ));          
    }  
    
    
    
     /**
     * @Route("/show/{id}/user/{userId}", name="showAgendaTemp")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     */
    public function showAction( Team $team, User $user)
    {        
        $agendaTemp = $this->getDoctrine()
                ->getRepository(AgendaTemp::class)
                ->findTempByUserByTeam($team, $user);              

            if (!$agendaTemp) {
                // si l'agendaTemp n'éxiste pas renvoie la route agenda :
                return $this->redirectToRoute('showAgenda');            
            }
                   
                                       
            //$agentBetweens = $this->getDoctrine()
               // ->getRepository(AgendaTemp::class)
               // ->findTempByUserByTeam($team, $user);
            //dump($agentBetweens);die;
            
            //crée un array des agents de la team
            $agentId = [];    
            $agents = $team->getAgents();
            foreach ($agents as $agent) {
                $agentId[] = $agent->getId();
            }
            
            $startDate = $team->getEvent()->getStartDate();
            $endDate = $team->getEvent()->getEndDate();
            
            //crée un array d'array des agendas
            $agentBetweens = [];
            For ($i=0; $i<count($agentId); $i++){
                $agentBetweens[] = $this->getDoctrine()
                    ->getRepository(AgendaTemp::class)
                    ->findAllTempBetweenDateByUser($startDate, $endDate, $agentId [$i], $user);
            }
            //dump ($agentBetweens);die;
            

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
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, Team $team, User $user)
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
            'userId' =>$user->getId()       
                    ));
    }
    
    /**
     * Persist temp in agenda entity.
     *
     * @Route("/valid/{id}/user/{userId}", name="validTemp")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     * @Method({"GET", "POST"})
     */
    public function validAction(Request $request, Team $team, User $user)
    {    
        $agendaToUpdates = $this->getDoctrine()
            ->getRepository(AgendaTemp::class)
            ->findTempByUserByTeam($team, $user);
        dump($agendaToUpdates);die;
        
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

