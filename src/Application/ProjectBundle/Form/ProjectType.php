<?php

namespace Application\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('category_id')
            ->add('type', 'choice', array(
                    'expanded' => true,
                    'choices'   => array('Idea', 'Beta', 'Startup')
                ))
            ->add('title')
            ->add('body')
            ->add('url')
            ->add('youtube_url')
        ;
    }

    public function getName()
    {
        return 'application_projectbundle_projecttype';
    }
}
