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
     * @return \RtxLabs\UserBundle\Model\GroupInterface
     */
    function createGroup();
}
