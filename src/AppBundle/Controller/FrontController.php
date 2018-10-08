<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends Controller
{
    /**
     * @Route("/see", name="seePlanning")
     */
    public function indexAction(Request $request)
    {       
        return $this->render('default/seePlanning.html.twig');
    }
}
