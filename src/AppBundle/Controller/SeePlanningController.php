<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends Controller
{
    /**
     * @Route("/front", name="homepage")
     */
    public function indexAction(Request $request)
    {       
        return $this->render('default/index.html.twig');
    }
}
