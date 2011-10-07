<?php

namespace Application\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('admin')
            ->add('facebook_id')
            ->add('category_id')
            ->add('email')
            ->add('name')
            ->add('body')
            ->add('location')
            //->add('date')
            ->add('votes')
            ->add('visits')
            ->add('freelance')
            ->add('url')
            ->add('linkedin_url')
            ->add('twitter_url')
            ->add('forrst_url')
            ->add('github_url')
            ->add('dribbble_url')
            ->add('flickr_url')
            ->add('youtube_url')
			->add('can_contact')
        ;
    }

    public function getName()
    {
        return 'application_userbundle_usertype';
    }
}
