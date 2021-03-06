<?php
namespace RtxLabs\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="rtxlabs_userbundle_index", options={"expose"="true"})
     */
    public function indexAction()
    {
        return new Response();
    }

    /**
     * @Route("/admin/user", name="rtxlabs_userbundle_admin_user")
     * @Route("/admin/user#list", name="rtxlabs_userbundle_admin_user_list")
     * @Route("/admin/user#list", name="uma")
     * @Route("/admin/user#group", name="rtxlabs_userbundle_admin_group")
     * @Route("/admin/user#group", name="ugma")
     * @Route("/admin/user#group/list", name="rtxlabs_userbundle_admin_group_list")
     * @Template("RtxLabsUserBundle:Admin:index.html.twig")
     */
    public function indexAdminAction()
    {
        $roleManager = $this->get('rtxlabs.user.rolemanager');
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('RtxLabsUserBundle:Group')->findAll();
        return array('roles' => $roleManager->getRoles(), 'groups' => $groups);
    }

    /**
     * @Route("/account", name="uac")
     * @Template("RtxLabsUserBundle:User:index.html.twig")
     */
    public function indexUserAction()
    {
        $roleManager = $this->get('rtxlabs.user.rolemanager');
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('RtxLabsUserBundle:Group')->findAll();
        return array('roles' => $roleManager->getRoles(), 'groups' => $groups);
    }
}
