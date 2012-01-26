<?php
namespace RtxLabs\UserBundle\Controller;

use RtxLabs\UserBundle\Form\UserFilterType;
use RtxLabs\UserBundle\Model\UserFilter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use RtxLabs\UserBundle\Entity\User;
use Rotex\Sbp\CoreBundle\Binder\Binder;
use Rotex\Sbp\CoreBundle\Binder\GetMethodBinder;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use Rotex\Sbp\CoreBundle\Dencoder\Dencoder;
use Symfony\Component\HttpFoundation\Response;

class UserController extends RestController
{
    /**
     * @Route("/user/index", name="rtxlabs_userbundle_user_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/user", name="rtxlabs_userbundle_user_list", requirements={"_method"="GET"}, options={"expose"="true"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $em->getRepository("RtxLabsUserBundle:User")->findAll();
        $binder = GetMethodBinder::create()->bind($user);

        return new Response(Dencoder::encode($binder->execute()));
    }

    /**
     * @Route("/user/{id}", name="rtxlabs_userbundle_user_read", requirements={"_method"="GET"}, options={"expose"="true"})
     */
    public function readAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->find('RtxLabsUserBundle:User', $id);

        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $binder = GetMethodBinder::create()
                    ->bind($user);

        return new Response(Dencoder::encode($binder->execute()));
    }

    /**
     * @Route("/user/{id}",name="rtxlabs_userbundle_user_delete", requirements={"_method"="DELETE"}, options={"expose"="true"})
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->find('RtxLabsUserBundle:User', $id);

        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $user->setDeletedAt(new \DateTime('now'));
        $this->persistAndFlush($this->getCurrentUser());

        return new Response();
    }

    /**
     * @Route("/user",name="rtxlabs_userbundle_user_create", requirements={"_method"="POST"}, options={"expose"="true"})
     */
    public function createAction()
    {
        $user = new User();

        $this->createDoctrineBinder()
                ->bind(Dencoder::decode($this->getRequest()->getContent()))
                ->field("plainPassword", $this->getRequest()->get('password'))
                ->to($user)
                ->execute();

        $this->persistAndFlush($user);

        //$this->get('sbp.core.mailer')->sendWelcomeEmailMessage($user);

        return new Response(Dencoder::encode($this->createDoctrineBinder()->bind($user)->execute()));
    }

    /**
     * @Route("/inventory/{id}", name="inventory_inventory_update", requirements={"_method"="PUT"}, options={"expose"="true"})
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->find('RtxLabsUserBundle:User', $id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user.');
        }


    }

    /**
     * @Route("/save/user/{id}", name="rtxlabs_bundle_user_save")
     * @Template("RtxLabsUserBundle:User:edit.html.twig")
     */
    public function saveAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $newUser = false;
        if ($id == -1) {
            $user = new User();
            $newUser = true;
        }
        else {
            $user = $em->find('RtxLabsUserBundle:User', $id);
        }

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user.');
        }

        $userForm = $this->createForm(new UserType(), $user);
        $userForm->bindRequest($this->getRequest());

        if ($userForm->isValid()) {

            if ($newUser) {
                $this->get('sbp.core.mailer')->sendWelcomeEmailMessage($user);
            }

            if ($user->getPlainPassword() != "sbp_unchanged_$%!//") {
                $user_manager = $this->get('sbp.core.user_manager');
                $user_manager->updatePassword($user);
            }

            $em->persist($user);
            $em->flush();

            $this->getRequest()->getSession()->setFlash('saved.successful', 1);
            return new \Symfony\Component\HttpFoundation\RedirectResponse(
                $this->generateUrl('rtxlabs_bundle_user_list')
            );
        }

        return array('form'=>$userForm->createView(),
                     'user'=>$user);
    }
}
