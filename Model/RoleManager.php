<?php
namespace RtxLabs\UserBundle\Model;

class RoleManager
{
    public function __construct($roles)
    {
        $this->roles = $roles;
    }

    public function addRoles($group, $roles)
    {
        $this->roles[$group] = $roles;
    }

    public function getRolesByGroup($group)
    {
        if (array_key_exists($group, $this->roles)) {
            return $this->roles[$group];
        }
        else {
            return array();
        }
    }

    public function getRoles()
    {
        return $this->roles;
    }

    protected $roles;
}