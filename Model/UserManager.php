<?php

namespace RtxLabs\UserBundle\Model;

use RtxLabs\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManager;

class UserManager implements UserProviderInterface
{
    public function __construct(EncoderFactoryInterface $encoderFactory, EntityManager $em, $class)
    {
        $this->class = $class;

        if (false !== strpos($this->class, ':')) {
            $this->class = $em->getClassMetadata($class)->name;
        }

        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Generates the confirmation token if it is not set.
     */
    public function generateConfirmationToken(\RtxLabs\UserBundle\Entity\UserInterface $user)
    {
        $user->setConfirmationToken($this->generateToken());
    }

    /**
     * Generates a token.
     */
    public function generateToken()
    {
        $bytes = false;
        if (function_exists('openssl_random_pseudo_bytes') && 0 !== stripos(PHP_OS, 'win')) {
            $bytes = openssl_random_pseudo_bytes(32, $strong);

            if (true !== $strong) {
                $bytes = false;
            }
        }

        // let's just hope we got a good seed
        if (false === $bytes) {
            $bytes = hash('sha256', uniqid(mt_rand(), true), true);
        }

        return base_convert(bin2hex($bytes), 16, 36);
    }


    public function updatePassword(UserInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            //$user->eraseCredentials();
        }
    }

    /**
     * @param string $username
     * @return \RtxLabs\UserBundle\Entity\UserInterface
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    function loadUserByUsername($username)
    {
        $userArray = $this->repository->findByUsername($username);

        if (empty($userArray)) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }

        return $userArray[0];
    }

    function loadUserByAttribute($attribute, $value) {
        try {
            return $this->repository->findOneByAttribute($attribute, $value);
        }
        catch (\Doctrine\ORM\NoResultException $e) {
            throw new \Symfony\Component\Security\Core\Exception\AuthenticationException("user with personnel number '$value' not found");
        }
    }

    /**
     * @abstract
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    function createUser()
    {
        $user = new $this->class();
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    function supportsClass($class)
    {
        return $class === $this->class;
    }

    /**
     * {@inheritDoc}
     */
    function refreshUser(UserInterface $user)
    {
//        if (!$user instanceof User) {
//            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
//        }

        return $this->loadUserByUsername($user->getUsername());
    }

    function saveUser(UserInterface $user) {
        $this->em->persist($user);
//        $this->em->flush();
    }

    protected function getEncoder(\RtxLabs\UserBundle\Entity\UserInterface $user)
    {
        return $this->encoderFactory->getEncoder($user);
    }

    protected $em;
    protected $encoderFactory;
    protected $class;

    /**
     * @var \RtxLabs\UserBundle\Entity\UserRepository
     */
    protected $repository;
}
