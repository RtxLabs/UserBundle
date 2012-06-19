<?php
namespace RtxLabs\UserBundle\Model;

interface GroupManagerInterface
{
    /**
     * @abstract
     * @param $groupName
     * @return mixed
     */
    function findGroupByName($groupName);

    /**
     * @abstract
     * @return \RtxLabs\UserBundle\Entity\GroupInterface
     */
    function createGroup();
}
