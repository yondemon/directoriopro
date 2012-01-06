<?php

namespace Application\AnunciosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PostType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            //->add('user_id')
            ->add('category_id')


			->add('type', 'choice', array(
			  	'expanded' => true,
			    'choices'   => array('Empleo', 'Freelance', 'Prácticas')//, 'Colaboración'
			))



            ->add('title')
            ->add('body')
            ->add('email','email')
			->add('price')
			->add('location')
            ->add('city_id')
            ->add('country_id')
            //->add('featured')
        ;
    }

    public function getName()
    {
        return 'application_anunciosbundle_posttype';
    }
}
