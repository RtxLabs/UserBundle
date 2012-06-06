<?php

namespace RtxLabs\UserBundle\Entity;

use RtxLabs\UserBundle\Model\UserFilter;

interface UserRepositoryInterface
{
    public function findAll();

    /**
     * @abstract
     * @param $id
     * @return UserInterface
     */
    public function find($id);

    /**
     * @abstract
     * @param $username
     * @return UserInterface
     */
    public function findOneByUsername($username);

    /**
     * @abstract
     * @param \RtxLabs\UserBundle\Model\UserFilter $filter
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFindByFilterQuery(UserFilter $filter);
}
