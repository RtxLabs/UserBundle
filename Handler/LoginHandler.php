<?php

namespace RtxLabs\UserBundle\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManager;

class LoginHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function onAuthenticationSuccess(Request $request,
                                            TokenInterface $token)
    {
        $user = $token->getUser();
        $session = $request->getSession();

        if ($user->getLocale()) {
            $request->getSession()->set('locale', $user->getLocale());
            $request->setLocale($user->getLocale());
        }

        $user->setLastLogin(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();
        
        if (($targetUrl = $session->get('_security.secured_device.target_path'))
            || ($targetUrl = $session->get('_security.secured_main.target_path'))) {

            $session->remove('_security.secured_device.target_path');
            $session->remove('_security.secured_main.target_path');
        }
        else {
            $targetUrl = '/';
        }
        
        return new RedirectResponse(0 !== strpos($targetUrl, 'http') ? $request->getUriForPath($targetUrl) : $targetUrl);
    }

    protected $em;
    protected $sm;
}
