<?php

namespace Application\EventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EventType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            //->add('user_id')
            ->add('title')
            ->add('body')
            ->add('date_start')
            ->add('date_end')
            //->add('featured')
            ->add('url')
            ->add('address')
            ->add('location')
            ->add('city_id')
            ->add('country_id')
            //->add('visits')
        ;
    }

    public function getName()
    {
        return 'application_eventbundle_eventtype';
    }
}
