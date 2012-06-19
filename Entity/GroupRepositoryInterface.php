<?php
namespace RtxLabs\UserBundle\Entity;

interface GroupRepositoryInterface
{
    public function findAll();
    public function findOneByName($name);
}
