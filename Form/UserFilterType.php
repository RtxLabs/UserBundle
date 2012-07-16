<?php

namespace RtxLabs\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserFilterType extends AbstractType
{
    public function getName()
    {
        return 'userFilter';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('lastname', 'text', array(
                                    'label'=>'rtxlabs.user.label.lastname',
                                    'required'=>false
                                          ));
        $builder->add('firstname', 'text', array(
                                    'label'=>'rtxlabs.user.label.firstname',
                                    'required'=>false
                                          ));
        $builder->add('username', 'text', array(
                                    'label'=>'rtxlabs.user.label.username',
                                    'required'=>false
                                          ));
        $builder->add('personnelNumber', 'text', array(
                                    'label'=>'rtxlabs.user.label.personnel_number',
                                    'required'=>false
                                          ));

    }
}
