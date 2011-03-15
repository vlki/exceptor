<?php

namespace Jp\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExceptionController extends Controller
{
    public function indexAction()
    {
        $em = $this->get("doctrine.orm.entity_manager");
        $list = $em
              ->createQuery(
                  'SELECT e, COUNT(e) as times, MAX(e.received_at) as rcvd ' .
                  'FROM ExceptorBundle:Exception e ' .
                  'GROUP BY e.signature ' .
                  'ORDER BY rcvd DESC'
                 )
                ->getResult();
        return $this->render('FrontendBundle:Exception:index.html.twig', array('list' => $list));
    }
    
    public function detailAction($signature)
    {
        $em = $this->get("doctrine.orm.entity_manager");
        $query = $em->createQuery('SELECT e FROM ExceptorBundle:Exception e WHERE e.signature = ?1 ORDER BY e.received_at DESC');
        $query->setParameter(1, $signature);
        $exceptions = $query->getResult();
        
        return $this->render('FrontendBundle:Exception:detail.html.twig', array('exceptions' => $exceptions));
    }
}
