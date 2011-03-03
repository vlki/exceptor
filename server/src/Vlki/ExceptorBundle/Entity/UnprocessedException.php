<?php

namespace Vlki\ExceptorBundle\Entity;

/**
 * Vlki\ExceptorBundle\Entity\UnprocessedException
 */
class UnprocessedException
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var text $data
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
     * Set data
     *
     * @param text $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get data
     *
     * @return text $data
     */
    public function getData()
    {
        return $this->data;
    }
}