<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 11/03/2017
 * Time: 14:34
 */

namespace UserBundle\DoctrineEvent;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use UserBundle\Entity\User;
use Swift_Mailer;

class MailUserListener implements EventSubscriber
{
    const EMAILCONFIRMATIONSUJET = "Demande d'inscription Validée";
    const EMAILREGISTERSUJET = "Nouvelle demande d'inscription";


    private $mailer;
    private $engine;

    public function __construct(Swift_Mailer $mailer, EngineInterface $engine)
    {
        $this->mailer = $mailer;
        $this->engine = $engine;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User){
            return;
        }

        $message = $this->mailer->createMessage()
            ->setSubject(self::EMAILREGISTERSUJET)
            ->setFrom($entity->getEmail())
            ->setTo('julien.moulis@moulis.me')
            ->setBody(
                $this->engine->render(
                    'email/registration.html.twig', [
                        'user' => $entity
                    ]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof User){
            return;
        }
        if($args->hasChangedField('isActive'))
        {
            if($entity->getIsActive() === true){
                $message = $this->mailer->createMessage()
                    ->setSubject(self::EMAILCONFIRMATIONSUJET)
                    ->setFrom('julien.moulis@mac.com')
                    ->setTo($entity->getEmail())
                    ->setBody(
                        $this->engine->render(
                            'email/confirmation.html.twig', [
                                'user' => $entity,
                            ]
                        ),
                        'text/html'
                    )
                ;
                $this->mailer->send($message);
            }
        }
    }

    public function getSubscribedEvents()
    {
        return ['postPersist', 'preUpdate'];
    }

}