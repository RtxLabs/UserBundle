<?php
namespace RtxLabs\UserBundle\Listener;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\SecurityContext;

class LoginListener
{
    protected $em, $router;

    public function __construct(EntityManager $em, RouterInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if($user) {
            $user->setLastLogin(new \DateTime());
            $user->setLoginAttempts(0);
            $this->em->persist($user);
            $this->em->flush();
        }
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $username = $event->getAuthenticationToken()->getUsername();
        $user = $this->em->getRepository("RtxLabsUserBundle:User")
            ->findOneByUsername($username);

        if($user instanceof UserInterface) {
            $loginAttempts = $user->getLoginAttempts();
            $loginAttempts = is_null($loginAttempts) ? 1 : ($loginAttempts+1);

            $user->setLoginAttempts($loginAttempts);
            $user->setLastLoginAttempt(new \DateTime());

            $this->em->persist($user);
            $this->em->flush();
        }

        return new RedirectResponse($this->router->generate('rtxlabs_userbundle_login'));
    }
}
