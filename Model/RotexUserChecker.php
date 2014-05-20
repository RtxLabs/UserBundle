<?php
namespace RtxLabs\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Validator\Constraints\DateTime;

class RotexUserChecker extends UserChecker
{
    public function checkPreAuth(UserInterface $user)
    {
        parent::checkPreAuth($user);

        $date = new \DateTime();
        $date->modify("-5 minutes");
        $loginAttempts = $user->getLoginAttempts();
        $lastLoginAttempt = $user->getLastLoginAttempt();

        if($lastLoginAttempt->getTimestamp() > $date->getTimestamp() && $loginAttempts == 100) {
            throw new DisabledException('Possible Brute Force');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        parent::checkPostAuth($user);
    }
}