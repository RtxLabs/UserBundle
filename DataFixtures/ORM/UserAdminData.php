<?php
namespace RtxLabs\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserAdminData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userClass = $this->container->getParameter("rtxlabs.user.class");

        $userAdmin = new $userClass();
        assert($userAdmin instanceof \RtxLabs\UserBundle\Entity\UserInterface);

        $userAdmin->setUsername('admin');
        $userAdmin->setLocale('de');
        $userAdmin->setEmail('demo@demo.de');
        $userAdmin->setLastName('Admin');
        $userAdmin->setFirstName('Admin');
        $userAdmin->addRole('ROLE_ADMIN');
        $userAdmin->addRole('ROLE_USER');

        $encoder = $this->container->get('security.encoder_factory')->getEncoder($userAdmin);
        $userAdmin->setPassword($encoder->encodePassword('admin', $userAdmin->getSalt()));

        $manager->persist($userAdmin);
        $manager->flush();
    }
}
