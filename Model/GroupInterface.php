<?php
namespace RtxLabs\UserBundle\Model;

interface GroupInterface
{
    function getId();

    function setName($name);
    function getName();

    function addUsers(UserInterface $user);
    function getUsers();

    function hasRole($role);
    function getRoles();
    function removeRole($role);
    function addRole($role);
    function setRoles(array $roles);

    function getCreatedAt();

    function getUpdatedAt();

    function getDeletedAt();
    function setDeletedAt($deletedAt);
}
