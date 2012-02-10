<?php

namespace RtxLabs\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * RtxLabs\UserBundle\Entity\Group
 *
 * @ORM\Table(name="rtxlabs_group")
 * @ORM\Entity(repositoryClass="RtxLabs\UserBundle\Entity\GroupRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Group
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="name", length="255")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     */
    protected $users;

    /**
     * @ORM\Column(name="roles", type="array")
     * @var ArrayCollection $roles
     */
    protected $roles;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var \DateTime
     *
     * @ORM\column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = array();
    }

    /**
     * Add users
     *
     * @param CuteFlow\CoreBundle\Entity\User $users
     */
    public function addUsers(\RtxLabs\userBundle\Entity\User $users)
    {
        $this->users[] = $users;
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->roles, true);
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = strtoupper($role);
        }
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @ORM\PrePersist
     * @return void
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * @ORM\PreUpdate
     * @return void
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param datetime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Get deletedAt
     *
     * @return datetime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}