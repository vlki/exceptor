<?php

namespace Vlki\ExceptorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
        Symfony\Component\HttpFoundation\Response,
        Vlki\ExceptorBundle\Entity\UnprocessedException;

class InputController extends Controller
{
    public function indexAction($key)
    {
        /** @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->get('request');

        $gzippedData = $request->request->get('data');

        $data = gzuncompress($gzippedData);
        if (!$data) {
            return new Response('Bad Request; Cannot be decompressed', 400);
        }

        $structure = unserialize($data);
        if (!$structure) {
            return new Response('Bad Request; Cannot be unserialized', 400);
        }

        if (!is_array($structure)) {
            return new Response('Bad Request; Structure must be array', 400);
        }

        foreach (array('server', 'get', 'post', 'session', 'exception') as $neededKey) {
            if (!isset($structure[$neededKey])) {
                return new Response("Bad Request; Structure key '{$neededKey}' needed", 400);
            }
        }

        var_dump($structure);

        $exception = new UnprocessedException();
        $exception->setData($data);

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($exception);
        $em->flush();

        return new Response('Ok');
    }
}
