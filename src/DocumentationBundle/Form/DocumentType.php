<?php

namespace DocumentationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fileTemporary', FileType::class, [
                'label' => false
            ])
            ->add('categorie', null, [
                'label' => false,
                'placeholder' => 'Sélectionner un dossier'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DocumentationBundle\Entity\Document'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_document_type';
    }
}
