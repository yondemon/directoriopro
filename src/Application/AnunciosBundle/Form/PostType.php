<?php

namespace Application\AnunciosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PostType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('user_id')
            ->add('category_id')
            ->add('title')
            ->add('body')
            ->add('price')
            ->add('date')
        ;
    }

    public function getName()
    {
        return 'application_anunciosbundle_posttype';
    }
}
