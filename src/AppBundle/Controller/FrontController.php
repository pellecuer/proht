<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {       
        return $this->redirectToRoute('showAgenda');
    }
    
    /**
     * @Route("/test", name="test")
     */
    public function testAction(Request $request)
    {       
        return $this->render('test.html.twig');
    }
    
    
    /**
     * @Route("/test2", name="test2")
     */
    public function test2Action(Request $request)
    {       
        return $this->render('test2.html.twig');
    }
    
    /**
     * @Route("/seeplanning", name="seeplanning")
     */
    public function seePlanningAction(Request $request)
    {       
        return $this->render('default/seePlanning.html.twig');
    }
    
    /**
     * @Route("/doc", name="doc")
     */
    public function docAction(Request $request)
    {       
        return $this->render('default/doc.html.twig');
    }
    
    
    /**
     * @Route("/codes", name="codes")
     */
    public function codesAction(Request $request)
    {       
        return $this->render('default/codes.html.twig');
    }
    
    /**
     * @Route("/reglementation", name="reglementation")
     */
    public function reglementationAction(Request $request)
    {       
        return $this->render('default/reglementation.html.twig');
    }
    
    /**
     * @Route("/createdatetranche", name="createdatetranche")
     */
    public function createDateTrancheAction(Request $request)
    {       
        return $this->render('default/createDateTranche.html.twig');
    }
    
    /**
     * @Route("/accueiladmin", name="accueiladmin")
     */
    public function accueilAdminAction(Request $request)
    {       
        return $this->render('default/accueilAdmin.html.twig');
    }
    
    /**
     * @Route("/droitsroles", name="droitsroles")
     */
    public function droitsRolesAction(Request $request)
    {       
        return $this->render('default/droitsRoles.html.twig');
    }
    
    /**
     * @Route("/datatables", name="datatables")
     */
    public function dataTablesAction(Request $request)
    {       
        return $this->render('dataTable.html.twig');
    }
    
    /**
     * @Route("/equipe", name="equipe")
     */
    public function equipeAction(Request $request)
    {       
        return $this->render('default/equipe.html.twig');
    }
    
    /**
     * @Route("/section", name="section")
     */
    public function sectionAction(Request $request)
    {       
        return $this->render('default/section.html.twig');
    }
    
    /**
     * @Route("/service", name="service")
     */
    public function serviceAction(Request $request)
    {       
        return $this->render('default/service.html.twig');
    }
    
    /**
     * @Route("/historique", name="historique")
     */
    public function historiqueAction(Request $request)
    {       
        return $this->render('default/historique.html.twig');
    }
}
