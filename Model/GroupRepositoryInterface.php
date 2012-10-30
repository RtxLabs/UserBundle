<?php
namespace RtxLabs\UserBundle\Model;

interface GroupRepositoryInterface
{
    public function findAll();
    public function findOneByName($name);
}
