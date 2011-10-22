<?php

namespace Application\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class ForgotEmailType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('email', 'email');
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Application\UserBundle\Entity\User');
    }

    public function getName()
    {
        return 'forgot';
    }
}