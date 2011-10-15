<?php

namespace Application\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class LoginType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('email', 'email')
        		->add('pass', 'password');
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Application\UserBundle\Entity\User');
    }

    public function getName()
    {
        return 'login';
    }
}