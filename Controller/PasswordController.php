<?php
namespace RtxLabs\UserBundle\Controller;

use RtxLabs\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use RtxLabs\DataTransformationBundle\Dencoder\Dencoder;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\RequestHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;

class PasswordController extends RestController
{
    public function indexAction()
    {
        return $this->render(
            'RtxLabsUserBundle:Password:passwordTemplate.html.twig',
            array('email' => '')
        );
    }

    public function sendAction(Request $request)
    {
        $data = $request->request->get('password');
        $user_manager = $this->get('rtxlabs.user.user_manager');
        $user = $user_manager->findUserByEmail($data['email']);

        $errorList = $this->get('validator')->validateValue($data['email'], new Email());
        if(count($errorList) > 0) {
            $this->get('session')->getFlashBag()->set('reset-error', 'rtxlabs.user.password.reset.mail.invalid');
        }
        else if($user instanceof \RtxLabs\UserBundle\Entity\User) {
            $user_manager->generatePasswordToken($user);
            $user_manager->saveUser($user);
            $this->get('session')->getFlashBag()->set('reset-send', 'rtxlabs.user.password.reset.send');
            $this->get('rtxlabs.user.mailer')->sendResettingEmailMessage($user);
        }
        else {
            $this->get('session')->getFlashBag()->set('reset-error', 'rtxlabs.user.password.reset.mail.notfound');
        }
        return $this->render(
            'RtxLabsUserBundle:Password:passwordTemplate.html.twig',
            array('email' => $data['email'])
        );
    }
    
    public function resetAction($token) 
    {
        $user_manager = $this->get('rtxlabs.user.user_manager');
        $user = $user_manager->findUserByPasswordToken($token);

        if (!$user) {
            return new RedirectResponse($this->container->get('router')->generate('rtxlabs_userbundle_login'));
        }

        return $this->render(
            'RtxLabsUserBundle:Password:resetTemplate.html.twig',
            array('token' => $token, 'errorList' => array())
        );
    }

    public function updateAction(Request $request, $token) 
    {
        $user_manager = $this->get('rtxlabs.user.user_manager');
        $user = $user_manager->findUserByPasswordToken($token);

        if (!$user) {
            return new RedirectResponse($this->container->get('router')->generate('rtxlabs_userbundle_login'));
        }
        $data = $request->request->get('password');

        if($data['password'] !== $data['passwordRepeat']) {
            $this->get('session')->getFlashBag()->set('reset-error', 'rtxlabs.user.validation.passwordRepeat');
            return $this->render('RtxLabsUserBundle:Password:resetTemplate.html.twig', array('token' => $token));
        }

        $binder = $this->createDoctrineBinder()
            ->bind($data)
            ->field("plainPassword", $data['password'])
            ->to($user);
        $binder->except("roles");
        $binder->execute();

        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            foreach ($errors as $violation) {
                $this->get('session')->getFlashBag()->set('reset-error', $violation->getMessage());
            }
            return $this->render('RtxLabsUserBundle:Password:resetTemplate.html.twig', array('token' => $token));
        }
        else if ($user->getPlainPassword() != $this->container->getParameter('password_placeholder') &&
            $user->getPlainPassword() != "") {
            $user->setPasswordToken(null);
            $user_manager->updatePassword($user);
            $user_manager->saveUser($user);
            return new RedirectResponse($this->container->get('router')->generate('rtxlabs_user_password_reset_confirm'));
        }
        return $this->render('RtxLabsUserBundle:Password:resetTemplate.html.twig', array('token' => $token));
    }

    public function confirmAction() 
    {
        $engine = $this->container->get('templating');
        $response = new Response();
        $response->setContent($engine->render('RtxLabsUserBundle:Password:confirmedTemplate.html.twig'));
        $response->headers->set('refresh', '10; URL='. $this->container->get('router')->generate('rtxlabs_userbundle_login'));
        return $response;
    }
}