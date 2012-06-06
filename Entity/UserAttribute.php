<?php

namespace RtxLabs\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RtxLabs\UserBundle\Entity\UserAttribute
 *
 * @ORM\Table(name="rtxlabs_user_attribute")
 * @ORM\Entity(repositoryClass="RtxLabs\UserBundle\Entity\UserAttributeRepository")
 */
class UserAttribute implements UserAttributeInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $value
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    /**
     * @var Rotex\Sbp\SecurityBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="Rotex\Sbp\SecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }
}