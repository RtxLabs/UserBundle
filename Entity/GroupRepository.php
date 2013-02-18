<?php

namespace RtxLabs\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Rotex\Sbp\CoreBundle\Model\FilterBag;

class GroupRepository extends EntityRepository
{
    public function countByFilter(FilterBag $filter)
    {
        return $this->createFilterQueryBuilder($filter)
            ->select("count(g.id)")
            ->getQuery()->getSingleScalarResult();
    }

    public function findByFilter(FilterBag $filter, $limit=100, $offset=0)
    {
        return $this->createFilterQueryBuilder($filter)
            ->select("g")
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    private function createFilterQueryBuilder(FilterBag $filter)
    {
        $builder = $this->createQueryBuilder("g")
            ->where('g.deletedAt IS NULL');

        if($filter->has('name')) {
            $builder->where("g.name = :name")
                ->setParameter("name", $filter->get('name'));
        }
        return $builder;
    }
}