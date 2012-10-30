<?php
namespace RtxLabs\UserBundle\Model;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use RtxLabs\UserBundle\Entity\Group;
use RtxLabs\UserBundle\Model\GroupRepositoryInterface;

class GroupManager implements  GroupManagerInterface
{
    /**
     * @var string $groupClass
     */
    private $groupClass;

    /**
     * @var \RtxLabs\UserBundle\Model\GroupRepositoryInterface
     */
    private $groupRepository;

    public function __construct(EntityManager $em, $groupClass)
    {
        $this->groupClass = $groupClass;
        $this->groupRepository = $em->getRepository($groupClass);
    }

    /**
     * @param $groupName
     * @return mixed
     * @throws
     */
    function findGroupByName($groupName)
    {
        try {
            return $this->groupRepository->findOneByName($groupName);
        }
        catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    function createGroup()
    {
        return new $this->groupClass();
    }
}
