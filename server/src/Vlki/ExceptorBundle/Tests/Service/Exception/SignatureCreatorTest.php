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

    public function testCreateSignature_sameExceptions_sameNotEmptySignature()
    {
        $e1 = $this->createNoTraceExceptionEntity();
        $e2 = $this->createNoTraceExceptionEntity();
        $uut = $this->createUut();

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertNotEmpty($signature1);
        $this->assertEquals($signature1, $signature2);
    }

    public function testCreateSignature_differentLine_differentSignature()
    {
        $e1 = $this->createNoTraceExceptionEntity();
        $e2 = $this->createNoTraceExceptionEntity();
        $uut = $this->createUut();

        $data = $e2->getExceptionData();
        $data['line'] = 7;
        $e2->setExceptionData($data);

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertNotEquals($signature1, $signature2);
    }

    public function testCreateSignature_differentClass_differentSignature()
    {
        $e1 = $this->createNoTraceExceptionEntity();
        $e2 = $this->createNoTraceExceptionEntity();
        $uut = $this->createUut();

        $data = $e2->getExceptionData();
        $data['class'] = 'SuperException';
        $e2->setExceptionData($data);

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertNotEquals($signature1, $signature2);
    }

    public function testCreateSignature_differentFile_differentSignature()
    {
        $e1 = $this->createNoTraceExceptionEntity();
        $e2 = $this->createNoTraceExceptionEntity();
        $uut = $this->createUut();

        $data = $e2->getExceptionData();
        $data['file'] = '/home/vlki/projects/exceptor/client/curl/example2.php';
        $e2->setExceptionData($data);

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertNotEquals($signature1, $signature2);
    }

    public function testCreateSignature_differentMessage_sameSignature()
    {
        $e1 = $this->createNoTraceExceptionEntity();
        $e2 = $this->createNoTraceExceptionEntity();
        $uut = $this->createUut();

        $data = $e2->getExceptionData();
        $data['message'] = 'some other testing exception';
        $e2->setExceptionData($data);

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertEquals($signature1, $signature2);
    }

    public function testCreateSignature_differentCode_differentSignature()
    {
        $e1 = $this->createNoTraceExceptionEntity();
        $e2 = $this->createNoTraceExceptionEntity();
        $uut = $this->createUut();

        $data = $e2->getExceptionData();
        $data['code'] = 645;
        $e2->setExceptionData($data);

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertNotEquals($signature1, $signature2);
    }

    public function testCreateSignature_differentNumberOfTraces_differentSignature()
    {
        $e1 = $this->createNoTraceExceptionEntity();
        $e2 = $this->createNoTraceExceptionEntity();
        $uut = $this->createUut();

        $data = $e2->getExceptionData();
        $data['trace'] = array(
            0 => array(
                'file' => '/home/vlki/projects/exceptor/client/curl/example.php',
                'line' => 10,
                'functions' => 'asdasd',
                'args' => array(),
            )
        );
        $e2->setExceptionData($data);

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertNotEquals($signature1, $signature2);
    }

    public function testCreateSignature_oneTraceRecordWithDifferentLine_differentSignature()
    {
        $e1 = $this->createSingleTraceExceptionEntity();
        $e2 = $this->createSingleTraceExceptionEntity();
        $uut = $this->createUut();

        $data = $e2->getExceptionData();
        $data['trace'][0]['line'] = 11;
        $e2->setExceptionData($data);

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertNotEquals($signature1, $signature2);        
    }

    public function testCreateSignature_oneTraceRecordWithDifferentBaseFilePathWithoutMask_differentSignature()
    {
        $e1 = $this->createSingleTraceExceptionEntity();
        $e2 = $this->createSingleTraceWithDifferentBaseFilePathExceptionEntity();
        $uut = $this->createUut();

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertNotEquals($signature1, $signature2);             
    }

    public function testCreateSignature_oneTraceRecordWithDifferentBaseFilePathWithShortMask_differentSignature()
    {
        $e1 = $this->createSingleTraceExceptionEntity();
        $e2 = $this->createSingleTraceWithDifferentBaseFilePathExceptionEntity();
        $uut = $this->createUutWithMask('~^/home/vlki/[a-z]+~');

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertNotEquals($signature1, $signature2);
    }

    public function testCreateSignature_oneTraceRecordWithDifferentBaseFilePathWithMask_sameSignature()
    {
        $e1 = $this->createSingleTraceExceptionEntity();
        $e2 = $this->createSingleTraceWithDifferentBaseFilePathExceptionEntity();
        $uut = $this->createUutWithMask('~^/home/vlki/projects/exceptor(_release2)?~');

        $signature1 = $uut->createSignature($e1);
        $signature2 = $uut->createSignature($e2);

        $this->assertEquals($signature1, $signature2);
    }

    public function testCreateSignature_badMaskRegularExpression_errorThrown()
    {
        $e = $this->createSingleTraceExceptionEntity();
        $uut = $this->createUutWithMask('~^/home/vlki/projects/exceptor(_relea');

        $this->setExpectedException('PHPUnit_Framework_Error');
        $uut->createSignature($e);
    }

    protected function createUut()
    {
        return new Uut();
    }

    protected function createUutWithMask($mask)
    {
        return new Uut($mask);
    }

    protected function createNoTraceExceptionEntity()
    {
        $e = new ExceptionEntity();
        $e->setExceptionData(array(
            'class' => 'Exception',
            'message' => 'testing exception',
            'code' => 546,
            'trace' => array(),
            'file' => '/home/vlki/projects/exceptor/client/curl/example.php',
            'line' => 6
        ));

        return $e;
    }

    protected function createSingleTraceExceptionEntity()
    {
        $e = new ExceptionEntity();
        $e->setExceptionData(array(
            'class' => 'Exception',
            'message' => 'testing exception',
            'code' => 546,
            'trace' => array(
                0 => array(
                    'file' => '/home/vlki/projects/exceptor/client/curl/example.php',
                    'line' => 10,
                    'functions' => 'asdasd',
                    'args' => array(),
                )
            ),
            'file' => '/home/vlki/projects/exceptor/client/curl/example.php',
            'line' => 6
        ));

        return $e;
    }

    protected function createSingleTraceWithDifferentBaseFilePathExceptionEntity()
    {
        $e = new ExceptionEntity();
        $e->setExceptionData(array(
            'class' => 'Exception',
            'message' => 'testing exception',
            'code' => 546,
            'trace' => array(
                0 => array(
                    'file' => '/home/vlki/projects/exceptor_release2/client/curl/example.php',
                    'line' => 10,
                    'functions' => 'asdasd',
                    'args' => array(),
                )
            ),
            'file' => '/home/vlki/projects/exceptor_release2/client/curl/example.php',
            'line' => 6
        ));

        return $e;
    }

}