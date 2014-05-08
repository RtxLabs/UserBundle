<?php
namespace RtxLabs\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use RtxLabs\DataTransformationBundle\Dencoder\Dencoder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use RtxLabs\UserBundle\Model\UserManager;
use Symfony\Component\Validator\ConstraintViolation;

class UserController extends RestController
{
    private $whitelist = array("id", "firstname", "lastname", "username",
        "passwordRequired", "plainPassword", "passwordRepeat", "locale", "active", "lastLogin");

    /**
     * @Route("/user", name="rtxlabs_userbundle_user_list", requirements={"_method"="GET"}, options={"expose"="true"})
     */
    public function listAction()
    {
        return $this->defaultListAction($this->whitelist);
    }

    /**
     * @Route("/user/{id}", name="rtxlabs_userbundle_user_read", requirements={"_method"="GET"}, options={"expose"="true"})
     */
    public function readAction($id)
    {
        $user = $this->findEntity($id);
        return $this->defaultReadAction($user, $this->whitelist);
    }

    /**
     * @Route("/user",name="rtxlabs_userbundle_user_create", requirements={"_method"="POST"}, options={"expose"="true"})
     */
    public function createAction()
    {
        return $this->defaultCreateAction($this->whitelist);
    }

    /**
     * @Route("/user/{id}", name="rtxlabs_userbundle_user_update", requirements={"_method"="PUT"}, options={"expose"="true"})
     */
    public function updateAction($id)
    {
        $user = $this->findEntity($id);
        return $this->defaultUpdateAction($user, $this->whitelist);
    }

    /**
     * @Route("/user/{id}",name="rtxlabs_userbundle_user_delete", requirements={"_method"="DELETE"}, options={"expose"="true"})
     */
    public function deleteAction($id)
    {
        $user = $this->getDoctrine()
            ->getRepository('RtxLabsUserBundle:User')
            ->findOneById($id);
        if($user) {
            if(!$this->getCurrentUser()->isAdmin()) {
                return $this->responseForbidden();
            }
            $user->setDeletedAt(new \DateTime('now'));
            $this->persistAndFlush($user);
        }
        return new Response();
    }

    protected function bindRequestData($user, $whitelist)
    {
        $data = Dencoder::decode($this->get('request_stack')->getCurrentRequest()->getContent());
        $binder = $this->createDataBinder($whitelist)->bind($data)->to($user)->except("password");

        $data->plainPassword = $data->plainPassword == "" ?
            $this->container->getParameter('password_placeholder') :
            $data->plainPassword;

        if (!$data->passwordRequired) {
            $password = "IAmADummyPasswordForValidation1";
            $data->plainPassword = $password;
            $data->passwordRepeat = $password;
            $user->setPassword($password);
            $user->setPlainPassword($password);
        }

        if ($this->getCurrentUser()->isAdmin()) {
            $binder->field("userRoles", $data->roles);

            if(isset($data->group)) {
                $user->getGroups()->clear();
                $groups = explode(",", $data->group);
                foreach($groups as $groupId) {
                    $group = $this->getDoctrine()
                        ->getRepository('RtxLabsUserBundle:Group')
                        ->findOneById($groupId);
                    if($group) {
                        $user->addGroup($group);
                    }
                }
            }
        }
        else {
            $binder->except("roles");
        }
        $binder->execute();
    }

    protected function findValidationErrors($entity)
    {
        $userManager = $this->get('rtxlabs.user.user_manager');
        $data = Dencoder::decode($this->get('request_stack')->getCurrentRequest()->getContent());
        $validator = $this->get('validator');
        $errors = $validator->validate($entity);

        $emailVerification = $data->email != $entity->getEmail();
        $passwordVerification =
            $data->plainPassword != $this->container->getParameter('password_placeholder') &&
            $data->plainPassword != "";

        $entity->setEmail($data->email);

        if ($entity->getPasswordRequired() && $emailVerification || $passwordVerification) {
            if(!$userManager->verifyPassword($entity, $data->password)) {
                $errors->add(new ConstraintViolation('rtxlabs.user.myaccount.edit.failed', null, array(), null, 'password', null));
            }
        }

        if ($entity->getPasswordRequired() && $data->plainPassword !== $data->passwordRepeat) {
            $errors->add(new ConstraintViolation('rtxlabs.user.validation.passwordRepeat', null, array(), null, 'plainPassword', null));
        }

        if ($entity->getPasswordRequired() && $passwordVerification && !count($errors)) {
            $userManager->updatePassword($entity);
        }

        return $errors;
    }

    protected function createEntityBinder(array $whitelist)
    {
        $binder = parent::createEntityBinder($whitelist)
            ->field('admin', function($user) { return $user->hasRole('ROLE_ADMIN'); })
            ->field('userRoles', function($user) { return $user->getUserRoles(); })
            ->field('plainPassword', $this->container->getParameter('password_placeholder'))
            ->field('passwordRepeat', $this->container->getParameter('password_placeholder'))
            ->join('groups', $this->createDoctrineBinder());

        return $binder;
    }

    private function responseForbidden($propertyPath = '', $message = 'rtxlabs.user.validation.forbidden')
    {
        $error = array('propertyPath' => $propertyPath, 'message' => $message);
        return new Response(Dencoder::encode($error), '403');
    }

    /**
     * @return \RtxLabs\UserBundle\Model\UserRepositoryInterface
     */
     protected function getRepository()
     {
        $repository = $this->getDoctrine()->getRepository($this->getEntityClass());
        assert($repository instanceof \RtxLabs\UserBundle\Entity\UserRepository);
        return $repository;
     }

    /**
     * @return \RtxLabs\UserBundle\Entity\User
     */
    protected function getEntityClass() {
        $user = $this->container->getParameter("rtxlabs.user.class");
        return $user;
    }
}
