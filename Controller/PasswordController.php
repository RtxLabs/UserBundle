<?php
namespace RtxLabs\UserBundle\Controller;

use RtxLabs\UserBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use RtxLabs\DataTransformationBundle\Dencoder\Dencoder;

class PasswordController {
    private static $PASSWORD_PLACEHOLDER = "sbp_unchanged_$%!//";

    /**
     * @Template("RtxLabsUserBundle:Password:passwordTemplate.html.twig")
     */
    public function indexAction()
    {
        return array();
    }
    
    /**
     * @Template("RtxLabsUserBundle:Password:resetTemplate.html.twig")
     */
    public function resetAction($token) 
    {
        return array();
    }
    
    /**
     * @Template("RtxLabsUserBundle:Password:confirmedTemplate.html.twig")
     */
    public function confirmAction() 
    {
        return array();
    }
}