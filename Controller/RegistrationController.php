<?php
namespace RtxLabs\UserBundle\Controller;

use RtxLabs\UserBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Rotex\Sbp\CoreBundle\Http\ValidationErrorResponse;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use RtxLabs\DataTransformationBundle\Dencoder\Dencoder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController extends RestController
{
    private static $PASSWORD_PLACEHOLDER = "Sbp_unchanged_1$%!//";

    /**
     * @Template("RtxLabsUserBundle:Registration:index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }
    
    public function registerAction()
    {
        $user_manager = $this->get('rtxlabs.user.user_manager');
        $user = $user_manager->createUser();
        $errors = $this->updateUser($user, $this->getRequest(), $user_manager);
        if (count($errors) > 0 ) {
            return new ValidationErrorResponse($errors);
        }

        $this->get('rtxlabs.user.mailer')->sendRegistrationEmailMessage($user);

        $userArray = $this->createUserBinder()->bind($user)->execute();
        $json = Dencoder::encode($userArray);
        return new Response($json);
    }
    
    public function confirmAction($token)
    {
        $user_manager = $this->get('rtxlabs.user.user_manager');
        $user = $user_manager->findUserByRegistrationToken($token);

        if (!$user) {
            return new RedirectResponse($this->container->get('router')->generate('rtxlabs_userbundle_login'));
        }
        $user->setRegistrationToken(null);
        $user->setActive(true);

        $user_manager->saveUser($user);
        $this->signin($user);
        return new RedirectResponse($this->container->get('router')->generate('rtxlabs_user_registration_register').'#confirmed');
    }

    protected function updateUser($user, $request, $user_manager)
    {
        $json = Dencoder::decode($request->getContent());
        $binder = $this->createDoctrineBinder()
            ->bind($json)
            ->field("plainPassword", $json->password)
            ->to($user);
        $binder->except("roles");
        $binder->execute();
        $user_manager->generateRegistrationToken($user);

        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $errors;
        }

        if ($user->getPlainPassword() != self::$PASSWORD_PLACEHOLDER &&
            $user->getPlainPassword() != "") {
            $user_manager->updatePassword($user);
        }

        $this->persistAndFlush($user);
        $user_manager->saveUser($user);

        return array();
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

    protected function signin(\RtxLabs\UserBundle\Model\AdvancedUserInterface $user) {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'secured_area', $user->getRoles());
        $session = $this->getRequest()->getSession();
        $session->set('_security_secured_area', serialize($token));
    }
}
