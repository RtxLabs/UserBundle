<?php
namespace RtxLabs\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use RtxLabs\UserBundle\Entity\Group;
use RtxLabs\DataTransformationBundle\Binder\Binder;
use RtxLabs\DataTransformationBundle\Binder\GetMethodBinder;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use RtxLabs\DataTransformationBundle\Dencoder\Dencoder;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends RestController
{
    private $whitelist = array("name", "roles", "userCount");
    /**
     * @Route("/usergroup", name="rtxlabs_userbundle_group_list", requirements={"_method"="GET"}, options={"expose"="true"})
     */
    public function listAction()
    {
        return $this->defaultListAction();
    }

    /**
     * @Route("/usergroup/{id}", name="rtxlabs_userbundle_group_read", requirements={"_method"="GET"}, options={"expose"="true"})
     */
    public function readAction($id)
    {
        $group = $this->findEntity($id);
        return $this->defaultReadAction($group);
    }

    /**
     * @Route("/usergroup",name="rtxlabs_userbundle_group_create", requirements={"_method"="POST"}, options={"expose"="true"})
     */
    public function createAction()
    {
        return $this->defaultCreateAction($this->whitelist);
    }

    /**
     * @Route("/usergroup/{id}", name="rtxlabs_userbundle_group_update", requirements={"_method"="PUT"}, options={"expose"="true"})
     */
    public function updateAction($id)
    {
        $group = $this->findEntity($id);
        return $this->defaultUpdateAction($group, $this->whitelist);
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

    protected function bindRequestData($group, $whitelist)
    {
        $data = Dencoder::decode($this->getRequest()->getContent());
        $binder = $this->createDataBinder($this->whitelist)->bind($data)->to($group);

        if ($this->getCurrentUser()->isAdmin()) {
            $binder->field("roles", explode(",", $data->roles));
        }
        else {
            $binder->except("roles");
        }
        $binder->execute();
    }

    protected function createEntityBinder()
    {
        $binder = $this->createDoctrineBinder()
            ->field('userCount', function($group) { return count($group->getUsers()); });

        return $binder;
    }
}
