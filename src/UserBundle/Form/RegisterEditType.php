<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 11/03/2017
 * Time: 13:32
 */

namespace UserBundle\Form;


use AppBundle\Form\AddressType;
use AppBundle\Form\PhoneType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;
use UserBundle\Repository\UserRepository;

class RegisterEditType extends AbstractType
{
    const STAFF = 'ROLE_STAFF';
    const YOUNGSTER = 'ROLE_YOUNGSTER';
    const EXTERNAL = 'ROLE_EXTERNAL';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('plainPassword')
            ->add('name', TextType::class)
            ->add('firstname', TextType::class)
            ->add('email', EmailType::class)
            ->add('phoneNumbers', CollectionType::class, [
                'entry_type' => PhoneType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false
            ])
            ->add('addresses', CollectionType::class,[
                'entry_type' => AddressType::class,
                'allow_delete' => true,
                'allow_add' => true,
                'by_reference' => false
            ])
            ->add('coach', EntityType::class, [
                'class' => User::class,
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function(UserRepository $repository){
                return $repository->createQueryBuilder('user')
                    ->andWhere('user.role != :role ')
                    ->setParameter('role', 'ROLE_YOUNGSTER');
                },
                'group_by' => 'role'
            ])
            ->add('titre')
            ->add('role', ChoiceType::class,
                [
                    'choices' => [
                        'Personnel' => self::STAFF,
                        'Jeune' => self::YOUNGSTER,
                        'Externe' => self::EXTERNAL
                    ]
                ])
            ->add('groups', null, [
                'label' => 'Groupe',
                'expanded' => true,
                'multiple' => true
            ])
            ->add('isActive')
            //->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSubmitData'))
        ;
    }

    public function onPreSubmitData(FormEvent $event)
    {
        if($event->getData() && $event->getData()->getId()){
            $form = $event->getForm();
            unset($form['plainPassword']);

            $roles = $event->getData()->getRoles();

            if($roles != 'ROLE_ADMIN') {
                unset($form['coach']);
                unset($form['isActive']);
                unset($form['role']);
            }
        }
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}