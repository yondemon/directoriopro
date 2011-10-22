<?php

namespace Application\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilder;


class ForgotPassType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('pass', 'repeated', array(
				           'first_name' => 'password',
				           'second_name' => 'confirm',
				           'type' => 'password',
							'invalid_message' => 'Las contraseñas tienen que coincidir',
				        ));
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Application\UserBundle\Entity\User');
    }

    public function getName()
    {
        return 'forgot';
    }
}