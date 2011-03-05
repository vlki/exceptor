<?php

namespace Vlki\ExceptorBundle\Service\Exception;

use Vlki\ExceptorBundle\Entity\Exception as ExceptionEntity,
    Vlki\ExceptorBundle\Service\Exception\Exception as ServiceException;

class SignatureCreator
{

    /** @var string|null */
    protected $mask;

    public function __construct($mask = null)
    {
        $this->mask = $mask;
    }

    public function createSignature(ExceptionEntity $exception)
    {
        $data = $exception->getExceptionData();

        if (empty($data)) {
            throw new ServiceException('No exception data to make signature from');
        }

        $file = $this->applyFileMask($data['file']);
        $trace = $this->createTraceString($data['trace']);

        return md5($data['class'] . ':' . $data['code'] . ':' . $file . ':' . $data['line'] . ':' . $trace);
    }

    protected function createTraceString($trace)
    {
        $traceRecords = array();
        
        foreach ($trace as $traceRecord) {
            $traceRecords[] = $this->applyFileMask($traceRecord['file']) . ':' . $traceRecord['line'];
        }

        return implode(':', $traceRecords);
    }

    protected function applyFileMask($filePath)
    {
        if (null === $this->mask) {
            return $filePath;
        }

        $maskedFilePath = preg_replace($this->mask, '', $filePath);

        if ($maskedFilePath === null) {
            throw new ServiceException("There was an error with applying the mask '{$this->mask}'.");
        }

        return $maskedFilePath;
    }

}