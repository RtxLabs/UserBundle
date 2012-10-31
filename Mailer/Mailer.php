<?php

namespace RtxLabs\UserBundle\Mailer;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;

class Mailer
{
    /**
     * @param use Symfony\Component\Routing\RouterInterface $router
     * @param Symfony\Component\Templating\EngineInterface $templating
     */
    public function __construct( RouterInterface $router,
                                 $mailer,
                                 EngineInterface $templating,
                                 array $parameters)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->router = $router;
        $this->parameters = $parameters;
    }

    /**
     * @return void
     */
    public function sendResettingEmailMessage($user)
    {
        assert($user instanceof \RtxLabs\UserBundle\Model\UserInterface ||
               $user instanceof \RtxLabs\UserBundle\Model\AdvancedUserInterface);
        $template = $this->parameters['resetting.template'].".html.twig";

        $url = $this->router->generate('rtxlabs_user_password_reset', array('token' => $user->getPasswordToken()), true);
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'homepage' => $url
        ));
        $this->sendEmailMessage($rendered, $user->getEmail());
    }

    /**
     * @return void
     */
    public function sendRegistrationEmailMessage($user)
    {
        assert($user instanceof \RtxLabs\UserBundle\Model\UserInterface ||
               $user instanceof \RtxLabs\UserBundle\Model\AdvancedUserInterface);
        $template = $this->parameters['registration.template'].".html.twig";

        $url = $this->router->generate('rtxlabs_user_registration_confirm', array('token' => $user->getRegistrationToken()), true);
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'homepage' => $url
        ));
        $this->sendEmailMessage($rendered, $user->getEmail());
    }

    /**
     * @return void
     */
    public function sendWelcomeEmailMessage($user)
    {
        assert($user instanceof \RtxLabs\UserBundle\Model\UserInterface ||
               $user instanceof \RtxLabs\UserBundle\Model\AdvancedUserInterface);
        $template = $this->parameters['welcome.template'].".html.twig";

        $url = $this->router->generate('core_dashboard', array(), true);
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'homepage' => $url
        ));
        $this->sendEmailMessage($rendered, $user->getEmail());
    }

    /**
     * @param string $renderedTemplate
     * @param string $toEmail
     * @param string $fromEmail
     * @return void
     */
    protected function sendEmailMessage($renderedTemplate, $toEmail, $fromEmail = null)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($toEmail)
            ->setFrom('info@rotex.de')          // TODO: Change to parameter
            ->setBody($body, 'text/html');

        if ($fromEmail != null) {
            $message->setFrom($fromEmail);
        }
        
        $this->mailer->send($message);
    }

    protected $mailer;
    protected $templating;
    protected $router;
    protected $parameters;
}
