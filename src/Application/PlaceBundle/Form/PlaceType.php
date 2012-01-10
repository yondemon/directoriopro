<?php

namespace Application\PlaceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('url')
            ->add('address')
            ->add('location')
            ->add('city_id')
            ->add('country_id')
            ->add('price')
        ;
    }

    public function getName()
    {
        return 'application_placebundle_placetype';
    }
}
