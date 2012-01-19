<?php
namespace RtxLabs\UserBundle\Controller;

use RtxLabs\UserBundle\Form\UserFilterType;
use RtxLabs\UserBundle\Model\UserFilter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use RtxLabs\UserBundle\Entity\User;
use RtxLabs\UserBundle\Form\UserType;

class UserController extends Controller
{
    /**
     * @Route("/admin/user", name="rtxlabs_bundle_user_list")
     * @Template()
     *
     * @return array
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $filter = $this->getRequest()->getSession()->get('user.filter', new UserFilter());

        $filterForm = $this->createForm(new UserFilterType(), $filter);
        $filterForm->bindRequest($this->getRequest());
        $this->getRequest()->getSession()->set('user.filter', $filter);

        $query = $em->getRepository('RtxLabsUserBundle:User')->getFindByFilterQuery($filter);
        $paginator = new \Pagerfanta\Pagerfanta(new \Pagerfanta\Adapter\DoctrineORMAdapter($query));
        $paginator->setMaxPerPage($this->container->getParameter('sbp.core.pagesize.default'));
        $paginator->setCurrentPage($this->get('request')->query->get('page', 1), false, true);

        return array('filterForm'=>$filterForm->createView(),
                     'paginator'=>$paginator);
    }

    /**
     * @Route("/admin/edit/user/{id}", name="rtxlabs_bundle_user_edit")
     * @Template()
     *
     * @param  $id
     * @return array
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->find('RtxLabsUserBundle:User', $id);

        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $user->setPlainPassword('sbp_unchanged_$%!//');

        $userForm = $this->createForm(new UserType(), $user);
        return array('form'=>$userForm->createView(),
                     'user'=>$user);
    }

    /**
     * @Route("/admin/create/user", name="rtxlabs_bundle_user_create")
     * @Template("RotexSbpCoreBundle:User:edit.html.twig")
     *
     * @return array
     */
    public function createAction()
    {
        $user = new User();
        $userForm = $this->createForm(new UserType(), $user);
        return array('form'=>$userForm->createView(),
                     'user'=>$user);
    }

    /**
     * @Route("/admin/save/user/{id}", name="rtxlabs_bundle_user_save")
     * @Template("RotexSbpCoreBundle:User:edit.html.twig")
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

    /**
     * @Route("/admin/delete/user/{id}", name="rtxlabs_bundle_user_delete")
     *
     * @param  $id
     * @return array
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->find('RtxLabsUserBundle:User', $id);

        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $user->setDeletedAt(new \DateTime('now'));
        $em->persist($user);
        $em->flush();

        $this->getRequest()->getSession()->setFlash('deleted.successful', 1);
        return new \Symfony\Component\HttpFoundation\RedirectResponse(
            $this->generateUrl('rtxlabs_bundle_user_list')
        );
    }
}
