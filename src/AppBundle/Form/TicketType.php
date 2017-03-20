<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Entity\User;
use UserBundle\Repository\UserRepository;

class TicketType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * {@inheritdoc}
     */


    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $builder
            ->add('objet')
            ->add('message')
            ->add('date', DateType::class, [
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('level', ChoiceType::class, [
                'choices' => [
                    'normal' => 'normal',
                    'urgent' => 'urgent'
                ]
            ])
            ->add('toWho', EntityType::class, [
                'class' => User::class,
                'placeholder' => 'Sélectionner un destinataire',
                'query_builder' => function(UserRepository $repository) {
                    return $repository->createQueryBuilder('user')
                        ->andWhere('user.role != :role ')
                        ->setParameter('role', 'ROLE_YOUNGSTER');
                }
            ])
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function(FormEvent $event) use ($user) {
                $form = $event->getForm();

                $formOptions = [
                    'class' => User::class,
                    'query_builder' => function(UserRepository $repository) use ($user){
                        return $repository->findYoungsterByCoach($user);
                    },
                ];

                $form->add('aboutWho', EntityType::class, $formOptions);
            })
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Ouvert' => 0,
                    'Clôturé' => 1
                ]
            ]);
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Ticket'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_ticket';
    }


}
