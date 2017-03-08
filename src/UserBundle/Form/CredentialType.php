<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CredentialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'Admin' => 'ROLE_ADMIN',
                    'Jeunes' => 'ROLE_MEMBER',
                    'Familles' => 'ROLE_MEMBER',
                    'Organisation' => 'ROLE_ORGANIZATION'
                ),
                'expanded' => true,
                'multiple' => true
            ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    public function setDefaultsOptions(OptionsResolver $resolver)
    {

    }

}
