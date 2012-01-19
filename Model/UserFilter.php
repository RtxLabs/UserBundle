<?php
namespace Rotex\Sbp\CoreBundle\Model;

class UserFilter
{
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPersonnelNumber($personnelNumber)
    {
        $this->personnelNumber = $personnelNumber;
    }

    public function getPersonnelNumber()
    {
        return $this->personnelNumber;
    }

    public function hasValues()
    {
        if ($this->firstname != "" ||
            $this->lastname != "" ||
            $this->username != "" ||
            $this->personnelNumber != "") {

            return true;
        }

        return false;
    }

    protected $firstname = null;
    protected $lastname = null;
    protected $username = null;
    protected $personnelNumber = null;
}
