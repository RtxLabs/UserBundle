<?php
namespace RtxLabs\UserBundle\Controller;

use RtxLabs\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rotex\Sbp\CoreBundle\Http\ValidationErrorResponse;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use RtxLabs\DataTransformationBundle\Dencoder\Dencoder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class RegistrationController extends RestController
{
    private $whitelist = array("username", "firstname", "lastname", "email", "locale");

    /**
     * @Template("RtxLabsUserBundle:Registration:index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }
    
    public function registerAction()
    {
        $json = Dencoder::decode($this->get('request_stack')->getCurrentRequest()->getContent());
        $userManager = $this->get('rtxlabs.user.user_manager');
        $user = $userManager->findUserByEmail($json->email);
        
        if($user instanceof User && $user->getDeletedAt() !== null) {
            $userManager->generateRegistrationToken($user);
            $userManager->saveUser($user);
            $this->get('rtxlabs.user.mailer')->sendReactivationEmailMessage($user);
            $response['success'] = false;
            $response['message'] = array('status' => 304);
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

        $userManager->saveUser($user);
        $this->get('rtxlabs.user.mailer')->sendRegistrationEmailMessage($user);

        return new Response(Dencoder::encode(array()));
    }
    
    public function confirmAction($token)
    {
        $userManager = $this->get('rtxlabs.user.user_manager');
        $user = $userManager->findUserByRegistrationToken($token);

        if (!$user) {
            return new RedirectResponse($this->container->get('router')->generate('rtxlabs_user_registration_expired'));
        }
        $user->setRegistrationToken(null);
        $user->setActive(true);

        $userManager->saveUser($user);
        $this->signin($user);
        return new RedirectResponse($this->container->get('router')->generate('rtxlabs_user_registration_confirmed'));
    }

    public function successAction()
    {
        return $this->render('RtxLabsUserBundle:Registration:successTemplate.html.twig');
    }

    public function expiredAction()
    {
        return $this->render('RtxLabsUserBundle:Registration:expiredTemplate.html.twig');
    }

    public function confirmedAction()
    {
        return $this->render('RtxLabsUserBundle:Registration:confirmedTemplate.html.twig');
    }

    protected function updateUser($user, $json, $userManager)
    {
        $binder =$this->createDataBinder($this->whitelist)
            ->bind($json)
            ->field("plainPassword", $json->password)
            ->to($user);
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

    protected function signin(\RtxLabs\UserBundle\Model\AdvancedUserInterface $user) {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'secured_area', $user->getRoles());
        $session = $this->get('request_stack')->getCurrentRequest()->getSession();
        $session->set('_security_secured_area', serialize($token));

        $event = new InteractiveLoginEvent($this->get('request_stack')->getCurrentRequest(), $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
    }
}
