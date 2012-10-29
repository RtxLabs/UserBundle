<?php
namespace RtxLabs\UserBundle\Controller;

use RtxLabs\UserBundle\Form\UserFilterType;
use RtxLabs\UserBundle\Entity\UserRepositoryInterface;
use RtxLabs\UserBundle\Model\UserFilter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use RtxLabs\DataTransformationBundle\Binder\Binder;
use RtxLabs\DataTransformationBundle\Binder\GetMethodBinder;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use RtxLabs\DataTransformationBundle\Dencoder\Dencoder;
use Symfony\Component\HttpFoundation\Response;

class UserController extends RestController
{
    private static $PASSWORD_PLACEHOLDER = "sbp_unchanged_$%!//";

    /**
     * @Route("/user/index", name="uma")
     * @Route("/user/index#create", name="ucr")
     * @Route("/user/index#account", name="uac")
     * @Template()
     */
    public function indexAction()
    {
        $roleManager = $this->get('rtxlabs.user.rolemanager');

        $em = $this->getDoctrine()->getEntityManager();
        $groups = $em->getRepository('RtxLabsUserBundle:Group')->findAll();

        return array('roles'=>$roleManager->getRoles(),
                     'groups'=>$groups);
    }

    /**
     * @Route("/user", name="rtxlabs_userbundle_user_list", requirements={"_method"="GET"}, options={"expose"="true"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->getUserRepository()->findAll();
        $userArray = $this->createUserBinder()->bind($user)->execute();
        $json = Dencoder::encode($userArray);

        return new Response($json);
    }

    /**
     * @Route("/user/{id}", name="rtxlabs_userbundle_user_read", requirements={"_method"="GET"}, options={"expose"="true"})
     */
    public function readAction($id)
    {
        $user = $this->getUserRepository()->find($id);

        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $userArray = $this->createUserBinder()->bind($user)->execute();
        $json = Dencoder::encode($userArray);

        return new Response($json);
    }

    /**
     * @Route("/user/{id}",name="rtxlabs_userbundle_user_delete", requirements={"_method"="DELETE"}, options={"expose"="true"})
     */
    public function deleteAction($id)
    {
        $user = $this->getUserRepository()->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
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
        $userClass = $this->getUserClass();
        $user = new $userClass();

        $errors = $this->updateUser($user, $this->getRequest());
        if (count($errors) > 0 ) {
            return new Response(Dencoder::encode($errors), 406);
        }

        $this->get('rtxlabs.user.mailer')->sendWelcomeEmailMessage($user);

        $userArray = $this->createUserBinder()->bind($user)->execute();
        $json = Dencoder::encode($userArray);

        return new Response($json);
    }

    /**
     * @Route("/user/{id}", name="rtxlabs_userbundle_user_update", requirements={"_method"="PUT"}, options={"expose"="true"})
     */
    public function updateAction($id)
    {
        $user = $this->getUserRepository()->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user.');
        }

        $errors = $this->updateUser($user, $this->getRequest());
        if (count($errors) > 0) {
            return new Response(Dencoder::encode($errors), 406);
        }

        $userArray = $this->createDoctrineBinder()->bind($user)->execute();
        $json = Dencoder::encode($userArray);

        return new Response($json);
    }


    protected function updateUser($user, $request)
    {
        $json = Dencoder::decode($request->getContent());
        $binder = $this->createDoctrineBinder()
            ->bind($json)
            ->field("plainPassword", $json->password)
            ->to($user);

        if ($this->getCurrentUser()->isAdmin()) {
            $binder->field("roles", explode(",", $json->roles));
        }
        else {
            $binder->except("roles");
        }

        $binder->execute();

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $result = array();
            foreach ($errors as $key=>$violation) {
                if ($violation instanceof \Symfony\Component\Validator\ConstraintViolation) {
                    $result[$violation->getPropertyPath()] = $violation->getMessage();
                }
                else {
                    $result[$key] = $violation;
                }
            }

            return $result;
        }

        if ($user->getPlainPassword() != self::$PASSWORD_PLACEHOLDER ||
            $user->getPlainPassword() != "") {

            $user_manager = $this->get('rtxlabs.user.user_manager');
            $user_manager->updatePassword($user);
        }

        $this->persistAndFlush($user);

        return array();
    }

    /**
     * @return \RtxLabs\UserBundle\Entity\UserRepositoryInterface
     */
    private function getUserRepository()
    {
        $repository = $this->getDoctrine()->getRepository($this->getUserClass());
        assert($repository instanceof UserRepositoryInterface);
        return $repository;
    }

    /**
     * @return \RtxLabs\UserBundle\Entity\UserInterface
     */
    private function getUserClass() {
        $user = $this->container->getParameter("rtxlabs.user.class");
        return $user;
    }

    private function createUserBinder()
    {
        $binder = $this->createDoctrineBinder()
            ->field('admin', function($user) { return $user->hasRole('ROLE_ADMIN'); })
            ->field('plainPassword', self::$PASSWORD_PLACEHOLDER)
            ->field('passwordRepeat', self::$PASSWORD_PLACEHOLDER)
            ->except("password")
            ->join('groups', $this->createDoctrineBinder());

        return $binder;
    }
}
