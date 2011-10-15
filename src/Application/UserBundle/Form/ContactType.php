<?php

namespace Application\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name')
        		->add('email', 'email')
        		->add('subject')
        		->add('body', 'textarea');
    }

    public function getName()
    {
        return 'contact';
    }
}