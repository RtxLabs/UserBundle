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
    /**
     * @Route("/usergroup/index", name="ugma")
     * @Route("/usergroup/index#new", name="ugcr")
     * @Template()
     */
    public function indexAction()
    {
        $roleManager = $this->get('rtxlabs.user.rolemanager');

        return array('roles'=>$roleManager->getRoles());
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
     * @Route("/usergroup/{id}", name="rtxlabs_userbundle_group_read", requirements={"_method"="GET"}, options={"expose"="true"})
     */
    public function readAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $group = $em->find('RtxLabsUserBundle:Group', $id);

        if (!$group) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $binder = GetMethodBinder::create()
                    ->bind($group);

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

    /**
     * @Route("/usergroup",name="rtxlabs_userbundle_group_create", requirements={"_method"="POST"}, options={"expose"="true"})
     */
    public function createAction()
    {
        $group = new Group();

        $errors = $this->updateGroup($group, $this->getRequest());
        if (count($errors) > 0) {
            return new Response(Dencoder::encode($errors), 406);
        }

        return new Response(Dencoder::encode($this->createDoctrineBinder()->bind($group)->execute()));
    }

    /**
     * @Route("/usergroup/{id}", name="rtxlabs_userbundle_group_update", requirements={"_method"="PUT"}, options={"expose"="true"})
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $group = $em->find('RtxLabsUserBundle:Group', $id);

        if (!$group) {
            throw $this->createNotFoundException('Unable to find group.');
        }

        $errors = $this->updateGroup($group, $this->getRequest());
        if (count($errors) > 0) {
            return new Response(Dencoder::encode($errors), 406);
        }

        return new Response(Dencoder::encode($this->createDoctrineBinder()->bind($group)->execute()));
    }


    protected function updateGroup($group, $request)
    {
        $json = Dencoder::decode($request->getContent());

        $this->createDoctrineBinder()
            ->bind($json)
            ->field('roles', explode(',', $json->roles))
            ->to($group)
            ->execute();

        $validator = $this->get('validator');
        $errors = $validator->validate($group);

        if (count($errors) > 0) {
            $result = array();
            foreach ($errors as $violation) {
                $result[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return $result;
        }
        else {
            $this->persistAndFlush($group);
            return true;
        }

        return array();
    }

}
