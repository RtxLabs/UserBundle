<?php
namespace RtxLabs\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserInterface as UI;

class RotexUserChecker extends UserChecker
{
    public function checkPreAuth(UI $user)
    {
        parent::checkPreAuth($user);

        $date = new \DateTime();
        $date->modify("-5 minutes");
        $loginAttempts = $user->getLoginAttempts();
        $lastLoginAttempt = $user->getLastLoginAttempt();

        if(!is_null($lastLoginAttempt)
            && $lastLoginAttempt->getTimestamp() > $date->getTimestamp() && $loginAttempts >= 100) {
            throw new DisabledException('Possible Brute Force');
        }
    }

    public function checkPostAuth(UI $user)
    {
        parent::checkPostAuth($user);
    }
}