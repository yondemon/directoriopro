<?php

namespace Application\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilder;


class CommentType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('body', 'textarea')
				->add('type', 'choice', array(
				    'choices'   => array('Idea', 'Bug')
				));
    }

    public function getName()
    {
        return 'comment';
    }
}