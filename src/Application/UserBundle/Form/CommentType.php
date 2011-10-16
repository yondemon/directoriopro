<?php

namespace Application\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilder;


class CommentType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('body', 'textarea')
				->add('type', 'choice', array(
				    'choices'   => array('Me gusta lo que hace', 'Quiero trabajar con esta persona', 'He trabajado con esta persona')
				));
    }

    public function getName()
    {
        return 'comment';
    }
}