<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 11/03/2017
 * Time: 13:32
 */

namespace UserBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;
use UserBundle\Repository\UserRepository;

class RegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe'
                    ],
                'second_options' => [
                    'label' => 'Confirmation'
                ],
                'invalid_message' => 'Les mots de passes ne correspondent pas'
            ])
            ->add('titre')
            ->add('role', ChoiceType::class,
                [
                    'label' => 'Rôle',
                    'placeholder' => 'Sélectionner',
                    'choices' => [
                        'Personnel' => 'ROLE_STAFF',
                        'Jeune' => 'ROLE_YOUNGSTER',
                        'Externe' => 'ROLE_EXTERNAL'
                    ]
                ])
            ->add('groups', null, [
                'label' => 'Groupe',
                'expanded' => true,
                'multiple' => true
            ])
            ->add('youngsters', EntityType::class, [
                'class' => User::class,
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function(UserRepository $repository) {
                    return $repository->createQueryBuilder('user')
                        ->where('user.role = :role')
                        ->setParameter('role', 'ROLE_YOUNGSTER');
                }

            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSubmitData'))
        ;
    }

    public function onPreSubmitData(FormEvent $event)
    {
        if($event->getData() && $event->getData()->getId()){
            $form = $event->getForm();
            unset($form['plainPassword']);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }

}