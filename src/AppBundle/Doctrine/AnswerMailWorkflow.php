<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 11/03/2017
 * Time: 14:34
 */

namespace AppBundle\Doctrine;


use AppBundle\Entity\Answer;
use AppBundle\Entity\Ticket;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Swift_Mailer;

class AnswerMailWorkflow implements EventSubscriber
{
    const NOUVEAUCOMMENTAIRE= "Nouveau Commentaire";


    private $mailer;
    private $engine;
    private $tokenStorage;

    public function __construct(Swift_Mailer $mailer, EngineInterface $engine, TokenStorageInterface $tokenStorage)
    {
        $this->mailer = $mailer;
        $this->engine = $engine;
        $this->tokenStorage = $tokenStorage;
    }

    // Gestion des réponses
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Answer){
            return;
        }
        $user = $this->tokenStorage->getToken()->getUser()->getEmail();
        $author = $entity->getTicket()->getFromWho()->getEmail();
        $destinataire = $entity->getTicket()->getToWho()->getEmail();
        $destinataireName = $entity->getTicket()->getToWho()->__toString();
        $body = $entity->getMessage();

        if($user == $author)
        {
            $this->sendEmail($user, $destinataire, $body, $destinataireName);
        }
        elseif ($user == $destinataire)
        {
            $this->sendEmail($user, $author, $body, $destinataireName);
        }

    }

    public function getSubscribedEvents()
    {
        return ['postPersist'];
    }

    private function sendEmail($auteur, $destinataire, $body, $destinataireName)
    {
        $message = $this->mailer->createMessage()
            ->setSubject(self::NOUVEAUCOMMENTAIRE)
            ->setFrom($auteur)
            ->setTo([$destinataire => $destinataireName])
            ->setCc('julien.moulis@moulis.me')
            ->setBody($body);
        ;
        $this->mailer->send($message);

        return $message;
    }

}