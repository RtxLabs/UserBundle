<?php

namespace RtxLabs\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\CallbackValidator;

class UserType extends AbstractType
{
    public function getName()
    {
        return 'user';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {

        $builder->add('lastName', 'text', array(
                                    'label'=>'rtxlabs.user.label.lastname'
                                          ));

        $builder->add('firstName', 'text', array(
                                    'label'=>'rtxlabs.user.label.firstname'
                                          ));

        $builder->add('personnelNumber', 'text', array(
                                    'label'=>'rtxlabs.user.label.personnel_number'
                                          ));

        $builder->add('email', 'email', array(
                                    'label'=>'rtxlabs.user.label.email'
                                          ));

        $builder->add('username', 'text', array(
                                    'label'=>'rtxlabs.user.label.username'
                                          ));

        $builder->add('plainPassword', 'repeated', array(
                                    'type'=>'password',
                                    'invalid_message' => 'rtxlabs.user.validation.password.match',
                                    'options' => array('label' => 'rtxlabs.user.label.password', 'always_empty'=>false),
                                    'error_bubbling'=>true,
                                          ));
        
        $builder->add('admin', 'checkbox', array(
                                    'label'=>'rtxlabs.user.label.has_admin_rights',
                                    'required'=>false,
                                           ));
    }
}
