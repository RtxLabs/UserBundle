<?php
namespace RtxLabs\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ReactivationController extends RestController
{
    /**
     * @Template("RtxLabsUserBundle:Reactivation:index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }

    public function reactivateAction($token)
    {
        $userManager = $this->get('rtxlabs.user.user_manager');
        $user = $userManager->findUserByRegistrationToken($token);

        if (!$user) {
            return new RedirectResponse($this->container->get('router')->generate('rtxlabs_user_registration_expired'));
        }
        $user->setRegistrationToken(null);
        $user->setDeletedAt(null);

        $userManager->saveUser($user);
        return new RedirectResponse($this->container->get('router')->generate('rtxlabs_user_reactivation_confirmed'));
    }
    
    public function confirmedAction()
    {
        return $this->render('RtxLabsUserBundle:Reactivation:confirmedTemplate.html.twig');
    }
}
