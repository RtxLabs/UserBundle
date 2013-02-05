<?php

namespace RtxLabs\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Rotex\Sbp\CoreBundle\Model\FilterBag;

class UserRepository extends EntityRepository
{
    public function countByFilter(FilterBag $filter)
    {
        return $this->createFilterQueryBuilder($filter)
            ->select("count(u.id)")
            ->getQuery()->getSingleScalarResult();
    }

    public function findByFilter(FilterBag $filter, $limit=100, $offset=0)
    {
        return $this->createFilterQueryBuilder($filter)
            ->select("u")
            ->getQuery()
            ->getResult();
    }

    private function createFilterQueryBuilder(FilterBag $filter)
    {
        $builder = $this->createQueryBuilder("u")
            ->where('u.deletedAt IS NULL')
            ->leftJoin("u.groups", "g");

        if($filter->has('firstname')) {
            $builder->where("u.firstname = :firstname")
                ->setParameter("firstname", $filter->get('firstname'));
        }
        if($filter->has('lastname')) {
            $builder->andWhere("u.lastname = :lastname")
                ->setParameter("lastname", $filter->get('lastname'));
        }
        if($filter->has('username')) {
            $builder->andWhere("u.username = :username")
                ->setParameter("username", $filter->get('username'));
        }
        if($filter->has('personnelNumber')) {
            $builder->andWhere("u.personnelNumber = :personnelNumber")
                ->setParameter("personnelNumber", $filter->get('personnelNumber'));
        }
        return $builder;
    }
}