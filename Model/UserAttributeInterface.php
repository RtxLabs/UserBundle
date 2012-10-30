<?php

namespace RtxLabs\UserBundle\Model;

interface UserAttributeInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value);

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();
}
