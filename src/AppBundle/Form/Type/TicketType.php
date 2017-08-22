<?php

namespace AppBundle\Form\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('objet', TextType::class)
            ->add('level', ChoiceType::class, [
                'choices' => [
                    'Normal' => 'normal',
                    'Urgent' => 'urgent'
                ]
            ])
            ->add('message')
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function(FormEvent $event) use ($user) {
                $form = $event->getForm();
                if($user->getRole() === 'ROLE_YOUNGSTER'){
                    $form
                        ->remove('toWho')
                        ->remove('aboutWho');

                } elseif ($user->getRole() === 'ROLE_ADMIN'){
                    $form->add('toWho', EntityType::class, [
                        'class' => User::class,
                        'placeholder' => 'Sélectionner un destinataire',
                        'query_builder' => function(UserRepository $repository) {
                            return $repository->createQueryBuilder('user')
                                ->andWhere('user.role != :role ')
                                ->setParameter('role', 'ROLE_YOUNGSTER');
                        }
                    ]);
                    $formOptions = [
                        'class' => User::class,
                        'placeholder' => 'Sélectionner le jeune',
                        'query_builder' => function(UserRepository $repository) {
                            return $repository->findAllYoungsters();
                        }
                    ];
                    $form->add('aboutWho', EntityType::class, $formOptions);
                } else {
                    $formOptions = [
                        'class' => User::class,
                        'placeholder' => 'Sélectionner le jeune',
                        'query_builder' => function(UserRepository $repository) use ($user){
                            return $repository->findMyYoungsters($user);
                        },
                    ];
                    $form->add('aboutWho', EntityType::class, $formOptions);
                }
            })
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
        return '';
    }


}
