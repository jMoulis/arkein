<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class MemberEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('credential')
            ->remove('save')
            ->add('credential', CredentialEditType::class)
            ->add('edit', SubmitType::class)
            ;
    }

    public function getParent()
    {
        return MemberType::class;
    }
}

