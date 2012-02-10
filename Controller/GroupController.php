<?php
namespace RtxLabs\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use RtxLabs\UserBundle\Entity\Group;
use Rotex\Sbp\CoreBundle\Binder\Binder;
use Rotex\Sbp\CoreBundle\Binder\GetMethodBinder;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use Rotex\Sbp\CoreBundle\Dencoder\Dencoder;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends RestController
{
    /**
     * @Route("/usergroup/index", name="rtxlabs_userbundle_usergroup_index")
     * @Template()
     */
    public function indexAction()
    {
        $roleManager = $this->get('sbp.core.rolemanager');

        return array();
    }

    /**
     * @Route("/usergroup", name="rtxlabs_userbundle_group_list", requirements={"_method"="GET"}, options={"expose"="true"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $groups = $em->getRepository("RtxLabsUserBundle:Group")->findAll();

        $binder = GetMethodBinder::create()
                                    ->bind($groups)
                                    ->field('userCount', function($group) {
                                            return count($group->getUsers());
                                        });

        return new Response(Dencoder::encode($binder->execute()));
    }

    /**
     * @Route("/usergroup/{id}",name="rtxlabs_userbundle_group_delete", requirements={"_method"="DELETE"}, options={"expose"="true"})
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $group = $em->find('RtxLabsUserBundle:Group', $id);

        if (!$group) {
            throw $this->createNotFoundException();
        }

        $group->setDeletedAt(new \DateTime('now'));
        $this->persistAndFlush($group);

        return new Response();
    }

}
