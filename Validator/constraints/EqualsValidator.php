<?php
namespace RtxLabs\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
 
class EqualsValidator extends ConstraintValidator
{
    /**
     * Checks if the passed values are equal.
     *
     * @param mixed      $value      The value that should be validated
     * @param mixed      $compare    The comparison value if there is no entity
     * @param Constraint $constraint The constrain for the validation
     *
     * @return Boolean Whether or not the values are equal
     */
    public function isValid($value, Constraint $constraint)
    {
        if(is_object($this->context->getRoot()) && $value !== $this->context->getRoot()->get($constraint->field)->getData()) {
             $this->setMessage($constraint->message);
             return false;
        }
        else if($constraint->compare === null) {
             $this->setMessage($constraint->nocompare);
             return false;
        }
        else if ($value !== $constraint->compare) {
             $this->setMessage($constraint->message);
             return false;
        }
         return true;
    }
}