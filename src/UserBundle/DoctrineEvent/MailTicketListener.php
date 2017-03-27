<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 11/03/2017
 * Time: 14:34
 */

namespace UserBundle\DoctrineEvent;


use AppBundle\Entity\Answer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Swift_Mailer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MailTicketListener implements EventSubscriber
{
    const NOUVEAUCOMMENTAIRE= "Nouveau Commentaire";


    private $mailer;
    private $engine;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(Swift_Mailer $mailer, EngineInterface $engine, TokenStorageInterface $tokenStorage)
    {
        $this->mailer = $mailer;
        $this->engine = $engine;
        $this->tokenStorage = $tokenStorage;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Answer){
            return;
        }
        $user = $this->tokenStorage->getToken()->getUser()->getEmail();
        $author = $entity->getTicket()->getFromWho()->getEmail();
        $destinataire = $entity->getTicket()->getToWho()->getEmail();
        $body = $entity->getMessage();

        if($user == $author)
        {
            $this->sendEmail($user, $destinataire, $body);
        }
        elseif ($user == $destinataire)
        {
            $this->sendEmail($user, $author, $body);
        }


    }

    public function getSubscribedEvents()
    {
        return ['postPersist'];
    }

    private function sendEmail($auteur, $destinataire, $body)
    {
        $message = $this->mailer->createMessage()
            ->setSubject(self::NOUVEAUCOMMENTAIRE)
            ->setFrom($auteur)
            ->setTo($destinataire)
            ->setCc('julien.moulis@moulis.me')
            ->setBody($body);
        ;
        $this->mailer->send($message);

        return $message;
    }

}