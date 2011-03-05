<?php

namespace Vlki\ExceptorBundle\Entity;

/**
 * Vlki\ExceptorBundle\Entity\Exception
 */
class Exception
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $received_at;

    /**
     * Superglobal variable $_SERVER
     *
     * @var array
     */
    private $sg_server;

    /**
     * Superglobal variable $_SESSION
     *
     * @var array
     */
    private $sg_session;

    /**
     * Superglobal variable $_GET
     *
     * @var array
     */
    private $sg_get;

    /**
     * Superglobal variable $_POST
     *
     * @var array
     */
    private $sg_post;

    /**
     * Data of exception. Should contain hash with keys class, message, code, file, line and trace.
     *
     * @var array
     */
    private $exception_data;

    /**
     * Signature of the exception. Exception with same signatures should be grouped.
     *
     * @var string
     */
    private $signature;


    /**
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $receivedAt
     */
    public function setReceivedAt(\DateTime $receivedAt)
    {
        $this->received_at = $receivedAt;
    }

    /**
     * @return \DateTime
     */
    public function getReceivedAt()
    {
        return $this->received_at;
    }

    /**
     * @param array $sgServer
     */
    public function setSgServer($sgServer)
    {
        $this->sg_server = $sgServer;
    }

    /**
     * @return array
     */
    public function getSgServer()
    {
        return $this->sg_server;
    }

    /**
     * @param array $sgSession
     */
    public function setSgSession($sgSession)
    {
        $this->sg_session = $sgSession;
    }

    /**
     * @return array
     */
    public function getSgSession()
    {
        return $this->sg_session;
    }

    /**
     * @param array $sgGet
     */
    public function setSgGet($sgGet)
    {
        $this->sg_get = $sgGet;
    }

    /**
     * @return array
     */
    public function getSgGet()
    {
        return $this->sg_get;
    }

    /**
     * @param array $sgPost
     */
    public function setSgPost($sgPost)
    {
        $this->sg_post = $sgPost;
    }

    /**
     * @return array
     */
    public function getSgPost()
    {
        return $this->sg_post;
    }

    /**
     * @param array $exceptionData
     */
    public function setExceptionData($exceptionData)
    {
        $this->exception_data = $exceptionData;
    }

    /**
     * @return array
     */
    public function getExceptionData()
    {
        return $this->exception_data;
    }

    /**
     * @param string $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

}