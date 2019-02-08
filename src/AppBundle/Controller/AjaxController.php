<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class AjaxController {
    
        /**
     * @Route(" /ajax_request", name=" ajax_request")
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404, message="Vous ne disposez pas de droits suffisants pour accèder à cette fonctionnalité; Vous devez avoir le role Administrateur")
     */
    public function ajaxAction(Request $request)
    {
        /* on récupère la valeur envoyée par la vue */
        $personnage = strtoupper($request->request->get('letter'));
        switch ($personnage){
            case 'H':
                $titre = 'Repos hebdo';
                $producteur = '35h minimum';
                break;
            case 'J':
                $titre = 'Journée de travail normal';
                $producteur = '7h45-16h30';
                break;
            case 'M':
                $titre = 'Horaire M';
                $producteur = '5h-13h';
                break;
            case 'D':
                $titre = '5h30';
                $producteur = '15h30';
                break;
            case 'R':
                $titre = 'Repos journalier';
                $producteur = '11h minimum';
        }
        /* la réponse doit être encodée en JSON ou XML, on choisira le JSON
         * la doc de Symfony est bien faite si vous devez renvoyer un objet         *
         */
        $response = new Response(json_encode(array(
            'titre' => $titre,
            'producteur' => $producteur
        )));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
      /**
     * @Route(" /dateAjax", name=" /dateAjax")
     */
    public function dateAjaxAction(Request $request)
    {
        /* on récupère la valeur envoyée par la vue */
        $startDate = strtoupper($request->request->get('startDate'));
         $endDate = strtoupper($request->request->get('endDate'));
        
        /* la réponse doit être encodée en JSON ou XML, on choisira le JSON
         * la doc de Symfony est bien faite si vous devez renvoyer un objet         *
         */
        $response = new Response(json_encode(array(
            'titre' => $startDate,
            'producteur' => $endDate
        )));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}