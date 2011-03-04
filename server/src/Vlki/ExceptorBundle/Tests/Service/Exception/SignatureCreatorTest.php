<?php

namespace Vlki\ExceptorBundle\Tests\Service\Exception;

use PHPUnit_Framework_TestCase as Test,
    Vlki\ExceptorBundle\Service\Exception\SignatureCreator as Uut,
    Vlki\ExceptorBundle\Entity\Exception as ExceptionEntity;

class SignatureCreatorTest extends Test
{

    public function testCreateSignature_noExceptionData_exceptionThrown()
    {
        $this->setExpectedException('\Vlki\ExceptorBundle\Service\Exception\Exception');
        $this->createUut()->createSignature(new ExceptionEntity());
    }

    protected function createUut()
    {
        return new Uut();
    }

}