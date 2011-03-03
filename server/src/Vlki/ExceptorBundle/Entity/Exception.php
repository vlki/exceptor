<?php

namespace Vlki\ExceptorBundle\Entity;

/**
 * Vlki\ExceptorBundle\Entity\Exception
 */
class Exception
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var \DateTime $received_at
     */
    private $received_at;

    /**
     * @var string $data
     */
    private $data;


    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set received_at
     *
     * @param \DateTime $receivedAt
     */
    public function setReceivedAt($receivedAt)
    {
        $this->received_at = $receivedAt;
    }

    /**
     * Get received_at
     *
     * @return \DateTime $receivedAt
     */
    public function getReceivedAt()
    {
        return $this->received_at;
    }

    /**
     * Set data
     *
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get data
     *
     * @return string $data
     */
    public function getData()
    {
        return $this->data;
    }
}