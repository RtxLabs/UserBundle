<?php
namespace RtxLabs\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Rotex\Sbp\CoreBundle\Controller\RestController;
use RtxLabs\DataTransformationBundle\Dencoder\Dencoder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use RtxLabs\UserBundle\Model\UserManager;

/**
 * @Route("/user", service="rtxlabs.user.user_controller")
 */
class UserController extends RestController
{
    private $whitelist = array("firstname", "lastname", "email", "username", 
        "passwordRequired", "plainPassword", "passwordRepeat", "admin", "locale");
    private static $PASSWORD_PLACEHOLDER = "Sbp_unchanged_1$%!//";
    protected $container;
    /**
     * @var EntityManager $em
     */
    protected $em;
    /**
     * @var UserManager $um
     */
    protected $um;
    
    public function __construct(ContainerInterface $container, EntityManager $em, UserManager $um)
    {
        $this->container = $container;
        $this->em = $em;
        $this->um = $um;
    }

    /**
     * @Route("/index", name="uma")
     * @Route("/index#create", name="ucr")
     * @Route("/index#account", name="uac")
     * @Template()
     */
    public function indexAction()
    {
        $roleManager = $this->get('rtxlabs.user.rolemanager');
        $groups = $this->em->getRepository('RtxLabsUserBundle:Group')->findAll();
        return array('roles' => $roleManager->getRoles(), 'groups' => $groups);
    }

    /**
     * @Route("/", name="rtxlabs_userbundle_user_list", requirements={"_method"="GET"}, options={"expose"="true"})
     */
    public function listAction()
    {
        return $this->defaultListAction();
    }

    /**
     * @Route("/{id}", name="rtxlabs_userbundle_user_read", requirements={"_method"="GET"}, options={"expose"="true"})
     */
    public function readAction($id)
    {
        $alert = $this->findEntity($id);
        return $this->defaultReadAction($alert);
    }

    /**
     * @Route("/",name="rtxlabs_userbundle_user_create", requirements={"_method"="POST"}, options={"expose"="true"})
     */
    public function createAction()
    {
        return $this->defaultCreateAction($this->whitelist);
    }
    
    /**
     * @Route("/{id}", name="rtxlabs_userbundle_user_update", requirements={"_method"="PUT"}, options={"expose"="true"})
     */
    public function updateAction($id)
    {
        $alert = $this->findEntity($id);
        return $this->defaultUpdateAction($alert, $this->whitelist);
    }

    /**
     * @Route("/{id}",name="rtxlabs_userbundle_user_delete", requirements={"_method"="DELETE"}, options={"expose"="true"})
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
        $data = Dencoder::decode($this->getRequest()->getContent());
        $binder = $this->createDataBinder($whitelist)->bind($data)->to($user)->except("password");
        
        if ($this->getCurrentUser()->isAdmin()) {
            $binder->field("roles", explode(",", $data->roles));
        }
        else {
            $binder->except("roles");
        }
        $binder->execute();
    }
    
    protected function findValidationErrors($entity)
    {
        $data = Dencoder::decode($this->getRequest()->getContent());
        $validator = $this->get('validator');
        $errors = $validator->validate($entity);
        
        if($data->plainPassword !== $data->passwordRepeat) {
            $errors[] = array('propertyPath' => 'passwordRepeat', 'message' => 'rtxlabs.user.validation.passwordRepeat');
        }
        if ($entity->getPlainPassword() != self::$PASSWORD_PLACEHOLDER &&
            $entity->getPlainPassword() != "" && !count($errors)) {

            $this->um->updatePassword($entity);
        }
        
        return $errors;
    }
    
    protected function createEntityBinder()
    {
        $binder = $this->createDoctrineBinder()
            ->field('admin', function($user) { return $user->hasRole('ROLE_ADMIN'); })
            ->field('plainPassword', self::$PASSWORD_PLACEHOLDER)
            ->field('passwordRepeat', self::$PASSWORD_PLACEHOLDER)
            ->except("password")
            ->join('groups', $this->createDoctrineBinder());

        return $binder;
    }

    private function responseForbidden($propertyPath = '', $message = 'rtxlabs.user.validation.forbidden')
    {
        $error = array('propertyPath' => $propertyPath, 'message' => $message);
        return new Response(Dencoder::encode($error), '403');
    }
}
