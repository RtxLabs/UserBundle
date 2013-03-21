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
    /**
     * @Template("RtxLabsUserBundle:Registration:index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }
    
    public function registerAction()
    {
        $json = Dencoder::decode($this->getRequest()->getContent());
        $userManager = $this->get('rtxlabs.user.user_manager');
        $user = $userManager->findUserByEmail($json->email);
        
        if($user instanceof User && $user->getDeletedAt() !== null) {
            $userManager->generateRegistrationToken($user);
            $userManager->saveUser($user);
            $this->get('rtxlabs.user.mailer')->sendReactivationEmailMessage($user);
            $response['success'] = false;
            $response['message'] = array(
                'status' => 304,
                'url'    => $this->container->get('router')->generate('rtxlabs_user_registration_register').'#reactivation'
            );
            return new Response(Dencoder::encode($response));
        }

        $user = $userManager->createUser();
        $errors = $this->updateUser($user, $json, $userManager);
        if(!$json->tos) {
            $errors[] = array('propertyPath' => 'tos', 'message' => 'rtxlabs.user.tos');
        }
        if (count($errors) > 0 ) {
            return new ValidationErrorResponse($errors);
        }
        
        $this->persistAndFlush($user);
        $userManager->saveUser($user);
        $this->get('rtxlabs.user.mailer')->sendRegistrationEmailMessage($user);

        $userArray = $this->createUserBinder()->bind($user)->execute();
        return new Response(Dencoder::encode($userArray));
    }
    
    public function confirmAction($token)
    {
        $userManager = $this->get('rtxlabs.user.user_manager');
        $user = $userManager->findUserByRegistrationToken($token);

        if (!$user) {
            return new RedirectResponse($this->container->get('router')->generate('rtxlabs_userbundle_login'));
        }
        $user->setRegistrationToken(null);
        $user->setActive(true);

        $userManager->saveUser($user);
        $this->signin($user);
        return new RedirectResponse($this->container->get('router')->generate('rtxlabs_user_registration_register').'#confirmed');
    }

    public function reactivateAction($token)
    {
        $userManager = $this->get('rtxlabs.user.user_manager');
        $user = $userManager->findUserByRegistrationToken($token);

        if (!$user) {
            return new RedirectResponse($this->container->get('router')->generate('rtxlabs_userbundle_login'));
        }
        $user->setRegistrationToken(null);
        $user->setDeletedAt(null);

        $userManager->saveUser($user);
        return new RedirectResponse($this->container->get('router')->generate('rtxlabs_user_registration_register').'#reactivation/confirmed');
    }

    protected function updateUser($user, $json, $userManager)
    {
        $binder = $this->createDoctrineBinder()
            ->bind($json)
            ->field("plainPassword", $json->password)
            ->to($user);
        $binder->except("roles");
        $binder->execute();
        $userManager->generateRegistrationToken($user);

        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if($json->password !== $json->passwordRepeat) {
            $errors[] = array('propertyPath' => 'passwordRepeat', 'message' => 'rtxlabs.user.validation.passwordRepeat');
        }
        if (count($errors) > 0) {
            return $errors;
        }

        if ($user->getPlainPassword() != $this->container->getParameter('password_placeholder') &&
            $user->getPlainPassword() != "") {
            $userManager->updatePassword($user);
        }
        return array();
    }

    private function createUserBinder()
    {
        $binder = $this->createDoctrineBinder()
            ->field('admin', function($user) { return $user->hasRole('ROLE_ADMIN'); })
            ->field('plainPassword', $this->container->getParameter('password_placeholder'))
            ->field('passwordRepeat', $this->container->getParameter('password_placeholder'))
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
