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
            ->add('date_start','date', array( 
			            //'attr' => array('class' => 'somedatefield'), 
			            'widget' => 'single_text', 
			            'format' => 'dd-MM-yyyy', 
			))
            ->add('date_end','date', array( 
			            //'attr' => array('class' => 'somedatefield'), 
			            'widget' => 'single_text', 
			            'format' => 'dd-MM-yyyy', 
			))
            //->add('featured')
            ->add('url')
            ->add('address')
            ->add('location')
            ->add('city_id')
            ->add('country_id')
            ->add('hashtag')
            ->add('resources')
            //->add('visits')


        ;
    }

    public function getName()
    {
        return 'application_eventbundle_eventtype';
    }
}
