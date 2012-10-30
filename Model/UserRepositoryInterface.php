<?php

namespace RtxLabs\UserBundle\Model;

use RtxLabs\UserBundle\Model\UserFilter;

interface UserRepositoryInterface
{
    public function findAll();

    /**
     * @abstract
     * @param $id
     * @return \RtxLabs\UserBundle\Model\AdvancedUserInterface
     */
    public function find($id);

    /**
     * @abstract
     * @param $username
     * @return \RtxLabs\UserBundle\Model\AdvancedUserInterface
     */
    public function findOneByUsername($username);

    /**
     * @abstract
     * @param \RtxLabs\UserBundle\Model\UserFilter $filter
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFindByFilterQuery(UserFilter $filter);
}
