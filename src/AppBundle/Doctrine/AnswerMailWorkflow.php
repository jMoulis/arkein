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
    private $tokenStorage;

    public function __construct(Swift_Mailer $mailer, TokenStorageInterface $tokenStorage)
    {
        $this->mailer = $mailer;
        $this->tokenStorage = $tokenStorage;
    }

    // Gestion des réponses
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Answer){
            return;
        }

        $answerWriter = $this->tokenStorage->getToken()->getUser()->getEmail();
        $ticketAuthor = $entity->getTicket()->getFromWho()->getEmail();
        $destinataireAnswer = $entity->getTicket()->getToWho()->getEmail();
        $body = $entity->getMessage();


        if($answerWriter == $ticketAuthor)
        {
            $destinataireName = $entity->getTicket()->getToWho()->getFullName();
            $this->sendEmail($answerWriter, $destinataireAnswer, $body, $destinataireName);
        }
        elseif ($answerWriter == $destinataireAnswer)
        {
            $destinataireName = $entity->getTicket()->getFromWho()->getFullName();
            $this->sendEmail($answerWriter, $ticketAuthor, $body, $destinataireName);
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
            ->setBody($body);
        ;
        $this->mailer->send($message);

        return $message;
    }

}