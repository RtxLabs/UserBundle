<?php
namespace RtxLabs\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
 
/**
* @Annotation
*/
class Equals extends Constraint
{
    public $message = 'These values are not equal';
    public $nocompare = 'The compare values is not set';
    public $compare = null;
}