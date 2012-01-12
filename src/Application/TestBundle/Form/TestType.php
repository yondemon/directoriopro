<?php

namespace Application\TestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TestType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('questions')
            ->add('date')
            ->add('featured')
            ->add('user_id')
            ->add('visits')
        ;
    }

    public function getName()
    {
        return 'application_testbundle_testtype';
    }
}
