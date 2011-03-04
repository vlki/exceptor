<?php

namespace Jp\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExceptionController extends Controller
{
    public function indexAction()
    {
        $em = $this->get("doctrine.orm.entity_manager");
        $list = $em->createQueryBuilder()->select('e')->from('ExceptorBundle:Exception', 'e')->orderBy('e.received_at', 'DESC')->getQuery()->getResult();
        return $this->render('FrontendBundle:Exception:index.html.twig', array('list' => $list));
    }
    
    public function detailAction($id)
    {
        $em = $this->get("doctrine.orm.entity_manager");
        $exception = $em->find("ExceptorBundle:Exception", $id);
        
        return $this->render('FrontendBundle:Exception:detail.html.twig', array('exception' => $exception));
    }
}
