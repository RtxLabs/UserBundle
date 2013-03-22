<?php
namespace RtxLabs\UserBundle\Model;

use Rotex\Sbp\CoreBundle\Model\FilterBag;
use Doctrine\ORM\EntityManager;
use Rotex\Sbp\CoreBundle\Command\OutputAware;

class ExpiredTokensManager extends OutputAware
{
    public function __construct(EntityManager $em, $registrationExpire, $passwordExpire)
    {
        $this->em                 = $em;
        $this->passwordExpire     = $passwordExpire;
        $this->registrationExpire = $registrationExpire;
    }
    
    public function checkTokens()
    {
        foreach($this->getUsersWithExpiredRegistrationToken() as $user) {
            $this->resetRegistrationToken($user);
        }

        foreach($this->getUsersWithExpiredPasswordToken() as $user) {
            $this->resetPasswordToken($user);
        }

        $this->writeln("");
        $this->writeln("# flushing");
        $this->em->flush();

        $this->writeln("# Done.");
    }

    protected function getUsersWithExpiredRegistrationToken()
    {
        $date = new \DateTime();
        $date->modify("-".$this->registrationExpire." day");

        $this->writeln("");
        $this->writeln("# Deleting users older than (createdAt): ". $date->format('d-m-Y H:i:s'));

        $filter = new FilterBag();
        $filter->set('active', false);
        $filter->set('createdAt', $date);
        $filter->set('hasRegistrationToken', true);

        return $this->getUserRepository()->findByFilter($filter);
    }

    protected function getUsersWithExpiredPasswordToken()
    {
        $date = new \DateTime();
        $date->modify("-".$this->passwordExpire." day");

        $this->writeln("");
        $this->writeln("# Reseting token for users older than (updatedAt): ". $date->format('d-m-Y H:i:s'));

        $filter = new FilterBag();
        $filter->set('updatedAt', $date);
        $filter->set('hasPasswordToken', true);

        return $this->getUserRepository()->findByFilter($filter);
    }

    protected function resetRegistrationToken($user)
    {
        $date = $user->getCreatedAt();
        $this->writeln("# ".$user->getUsername() ." (createdAt: ". $date->format('d-m-Y H:i:s').")");
        $this->em->remove($user);
    }

    protected function resetPasswordToken($user)
    {
        $date = $user->getUpdatedAt();
        $this->writeln("# ".$user->getUsername() ." (updatedAt: ". $date->format('d-m-Y H:i:s').")");
        $user->setPasswordToken(null);
        $this->em->persist($user);
    }

    protected function getUserRepository()
    {
        return $this->em->getRepository('RtxLabsUserBundle:User');
    }

    protected $em;
    protected $parameters;
}