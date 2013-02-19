<?php
namespace RtxLabs\UserBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Rotex\Sbp\CoreBundle\Menu\MenuContainer;

class MenuBuilder
{
    private $securityContext;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function buildMenuItems(MenuContainer $container, Request $request)
    {
        $user = $this->securityContext->getToken()->getUser();

        if ( $user && ($user->hasRole('ROLE_ADMIN'))) {
            $menu = $container->findOrGenerateMenu('administration');

            $menu->addChild('core.menu.settings.user', array('route' => 'rtxlabs_userbundle_admin_user_list'));
            $menu->addChild('core.menu.settings.usergroup', array('route' => 'rtxlabs_userbundle_admin_group_list'));
        }
    }
}