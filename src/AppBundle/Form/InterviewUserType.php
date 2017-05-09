<?php

namespace AppBundle\Form;

use AppBundle\Entity\InterviewUser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;

class InterviewUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => false
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'true' => true,
                    'false' => false
                ]
            ])
            /*->addEventListener(
                FormEvents::POST_SET_DATA,
                [$this, 'onPostSetData']
            )*/
        ;
    }

    public function onPostSetData(FormEvent $event)
    {
        if ($event->getData() && $event->getData()->getId()) {
            $form = $event->getForm();
            unset($form['user']);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => InterviewUser::class
        ));
    }

    public function getBlockPrefix()
    {
        return '';
    }


}
