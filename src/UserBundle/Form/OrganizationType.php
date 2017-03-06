<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('credential', CredentialType::class)
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('dateCreated', DateType::class, array(
                'format' => 'ddMMyyyy'
            ))
            ->add('size', IntegerType::class)
            ->add('namecontact', TextType::class)
            ->add('firstnamecontact', TextType::class)
            ->add('address', TextareaType::class)
            ->add('phone', TextType::class)
            ->add('email', EmailType::class)
            ->add('facebook', UrlType::class)
            ->add('website', UrlType::class)
            ->add('save', SubmitType::class)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\Organization'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_organization';
    }


}
