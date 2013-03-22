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
            ->orderBy("u.lastname")
            ->setMaxResults($limit)
            ->setFirstResult($offset)
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
        if($filter->has('active')) {
            $builder->andWhere("u.active = :active")
                ->setParameter("active", $filter->get('active'));
        }
        if($filter->has('hasPasswordToken')) {
            if($filter->get('hasPasswordToken')) {
                $builder->andWhere("u.passwordToken IS NOT NULL");
            }
            else {
                $builder->andWhere("u.passwordToken IS NULL");
            }
        }
        if($filter->has('hasRegistrationToken')) {
            if($filter->get('hasRegistrationToken')) {
                $builder->andWhere("u.registrationToken IS NOT NULL");
            }
            else {
                $builder->andWhere("u.registrationToken IS NULL");
            }
        }
        if($filter->has('createdAt')) {
            $builder->andWhere("u.createdAt <= :date")
                    ->setParameter("date", $filter->get('createdAt'));
        }
        if($filter->has('updatedAt')) {
            $builder->andWhere("u.updatedAt <= :date")
                    ->setParameter("date", $filter->get('updatedAt'));
        }
        return $builder;
    }

    public function findOneByAttribute($attribute, $value)
    {
        $query = $this->createQueryBuilder('u')
            ->innerJoin('u.attributes', 'a')
            ->where('a.name = :attribute')->setParameter('attribute', $attribute)
            ->andWhere('a.value = :value')->setParameter('value', $value)
            ->getQuery();

        $user = $query->getSingleResult();

        return $user;
    }
}