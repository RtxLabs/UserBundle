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
     * @Route("/user/index#new", name="ucr")
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
        $binder = $this->createDoctrineBinder()
                        ->bind($user)
                        ->field('admin', function($user) {
                            return $user->hasRole('ROLE_ADMIN');
                        })
                        ->join('groups', $this->createDoctrineBinder());

        return new Response(Dencoder::encode($binder->execute()));
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

        $binder = $this->createDoctrineBinder()
                    ->bind($user)
                    ->field('admin', $user->hasRole('ROLE_ADMIN'))
                    ->field('plainPassword', self::$PASSWORD_PLACEHOLDER)
                    ->field('passwordRepeat', self::$PASSWORD_PLACEHOLDER)
                    ->join('groups', $this->createDoctrineBinder());

        return new Response(Dencoder::encode($binder->execute()));
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

        $this->get('sbp.core.mailer')->sendWelcomeEmailMessage($user);

        return new Response(Dencoder::encode($this->createDoctrineBinder()->bind($user)->execute()));
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

        return new Response(Dencoder::encode($this->createDoctrineBinder()->bind($user)->execute()));
    }


    protected function updateUser($user, $request)
    {
        $json = Dencoder::decode($request->getContent());
        $this->createDoctrineBinder()
            ->bind($json)
            ->field("roles", explode(",", $json->roles))
            ->field("plainPassword", $json->password)
            ->to($user)
            ->execute();

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if ($json->password != $json->passwordRepeat) {
            $errors['passwordRepeat'] = "Passwords doesn't match";
        }

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
    private function getUserRepository() {
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
}
