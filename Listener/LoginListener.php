<?php
namespace RtxLabs\UserBundle\Listener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;

class LoginListener
{
    protected $doctrine;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if($user) {
            $user->setLastLogin(new \DateTime());
            $this->em->persist($user);
            $this->em->flush();
        }
    }
}
