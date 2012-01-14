<?php

namespace Application\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('category_id')
            ->add('email','email')
            ->add('name')
            ->add('body')
            ->add('location')
            ->add('freelance')
            ->add('url')
            ->add('linkedin_url')
            ->add('twitter_url')
            ->add('forrst_url')
            ->add('github_url')
            ->add('dribbble_url')
            ->add('flickr_url')
            ->add('youtube_url')
            ->add('stackoverflow_url')
            ->add('vimeo_url')
            ->add('delicious_url')
            ->add('pinboard_url')
            ->add('itunes_url')
            ->add('android_url')
            ->add('chrome_url')
            ->add('masterbranch_url')
			->add('can_contact')
			->add('avatar_type')
			->add('unemployed')
			->add('city_id')
			->add('country_id')
			->add('search_team')
			->add('newsletter')
        ;
    }

    public function getName()
    {
        return 'application_userbundle_usertype';
    }
}
