<?php
namespace Rotex\Sbp\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Rotex\Sbp\CoreBundle\Entity\User;

class UserAdminData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setLocale('en');
        $userAdmin->setPersonnelNumber('1081');
        $userAdmin->setEmail('demo@demo.de');
        $userAdmin->setLastName('Admin');
        $userAdmin->setFirstName('Admin');
        $userAdmin->addRole('ROLE_ADMIN');
        $userAdmin->addRole('ROLE_USER');
        $userAdmin->addRole('ROLE_INVENTORY');

        $encoder = $this->container->get('security.encoder_factory')->getEncoder($userAdmin);
        $userAdmin->setPassword($encoder->encodePassword('admin', $userAdmin->getSalt()));

        $manager->persist($userAdmin);
        $manager->flush();
    }
}
