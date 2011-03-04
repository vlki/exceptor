<?php

namespace Vlki\ExceptorBundle\Service\Exception;

use Vlki\ExceptorBundle\Entity\Exception as ExceptionEntity,
    Vlki\ExceptorBundle\Service\Exception\Exception as ServiceException;

class SignatureCreator
{

    public function createSignature(ExceptionEntity $exception)
    {
        $data = $exception->getExceptionData();

        if (empty($data)) {
            throw new ServiceException('No exception data to make signature from');
        }

        return md5($data['class'] . ':' . $data['code'] . ':' . $data['file'] . ':' . $data['line']);
    }

}