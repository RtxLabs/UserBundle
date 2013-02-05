<?php
namespace RtxLabs\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController extends Controller
{
    /**
     * @Route("/admin/user", name="rtxlabs_userbundle_admin_user")
     * @Route("/admin/user#list", name="rtxlabs_userbundle_admin_user_list")
     * @Route("/adwin/user#edit", name="rtxlabs_userbundle_admin_user_edit")
     * @Route("/admin/user#create", name="rtxlabs_userbundle_admin_user_create")
     * @Template()
     */
    public function indexAction()
    {
        $roleManager = $this->get('rtxlabs.user.rolemanager');

        $em = $this->getDoctrine()->getEntityManager();
        $groups = $em->getRepository('RtxLabsUserBundle:Group')->findAll();

        return array('roles' => $roleManager->getRoles(), 'groups' => $groups);
    }
}
