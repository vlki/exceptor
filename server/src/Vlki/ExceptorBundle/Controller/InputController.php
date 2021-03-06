<?php

namespace Vlki\ExceptorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Vlki\ExceptorBundle\Entity\Exception as ExceptionEntity,
    Vlki\ExceptorBundle\Service\Exception\SignatureCreator,
    DateTime;

class InputController extends Controller
{
    public function indexAction($key)
    {
        /** @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->get('request');

        /** @var $logger \Symfony\Bundle\ZendBundle\Logger\Logger */
        $logger = $this->get('logger');

        $requestData = $request->request->get('data');

        $data = gzuncompress($requestData);
        if (!$data) {
            // try decoding
            $data = gzuncompress(urldecode($requestData));

            if (!$data) {
                $logger->debug("Cannot decompress data");
                return new Response('Bad Request; Cannot be decompressed', 400);
            }
        }

        $structure = unserialize($data);
        if (!$structure) {
            $logger->debug("Cannot unserialize data");
            return new Response('Bad Request; Cannot be unserialized', 400);
        }

        if (!is_array($structure)) {
            $logger->debug("Sent structure is not an array");
            return new Response('Bad Request; Structure must be array', 400);
        }

        foreach (array('server', 'get', 'post', 'session', 'exception') as $neededKey) {
            if (!isset($structure[$neededKey])) {
                $logger->debug("Structure key '{$neededKey}' not found");
                return new Response("Bad Request; Structure key '{$neededKey}' needed", 400);
            }
        }

        if (!is_array($structure['exception'])) {
            $logger->debug("Structure value with key exception is not an array");
            return new Response('Bad Request; Structure value with key exception must be array', 400);
        }

        foreach (array('class', 'message', 'code', 'trace', 'file', 'line') as $neededExceptionKey) {
            if (!isset($structure['exception'][$neededExceptionKey])) {
                $logger->debug("Bad Request; Structure key 'exception.{$neededKey}' needed");
                return new Response("Bad Request; Structure key 'exception.{$neededKey}' needed", 400);
            }
        }

        $exception = new ExceptionEntity();
        $exception->setSgServer($structure['server']);
        $exception->setSgGet($structure['get']);
        $exception->setSgPost($structure['post']);
        $exception->setSgSession($structure['session']);
        $exception->setExceptionData($structure['exception']);
        $exception->setReceivedAt(new DateTime());

        $signatureCreator = new SignatureCreator('~^/home/fastgsm/production/releases/[0-9]{14}~');
        $exception->setSignature($signatureCreator->createSignature($exception));

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($exception);
        $em->flush();

        return new Response('Ok');
    }
}
