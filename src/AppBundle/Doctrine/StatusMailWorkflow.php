<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 11/03/2017
 * Time: 14:34
 */

namespace AppBundle\Doctrine;

use AppBundle\Entity\InterviewUser;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Swift_Mailer;

class StatusMailWorkflow implements EventSubscriber
{
    const NOUVEAUSTATUS= "Nouveau status de présence";
    const ADMINMAIL = 'julien.moulis21@gmail.com';


    private $mailer;
    private $tokenStorage;

    public function __construct(Swift_Mailer $mailer, TokenStorageInterface $tokenStorage)
    {
        $this->mailer = $mailer;
        $this->tokenStorage = $tokenStorage;
    }

    public function preUpdate(PreUpdateEventArgs $args){

        $entity = $args->getEntity();
        if (!$entity instanceof InterviewUser){
            return;
        }

        $entretien = $args->getObject()->getInterview();
        $author = $this->tokenStorage->getToken()->getUser()->getEmail();
        $objet = 'Mise à jour de présence';

        $destinataire = $entretien->getAuthor()->getEmail();

        if($args->hasChangedField('status')){
            $body =
                '<html>' .
                '<head></head>' .
                '<body>' .
                '<p></p>'.
                '</body>' .
                '</html>';
            $this->sendEmail($objet, $author, $destinataire, $body);
        }
    }

    public function getSubscribedEvents()
    {
        return ['preUpdate'];
    }

    private function sendEmail($objet, $auteur, $destinataire, $body)
    {
        $message = $this->mailer->createMessage()
            ->setSubject("Invitation à un entretien: " . $objet)
            ->setFrom($auteur)
            ->setTo($destinataire)
            ->setCc(self::ADMINMAIL)
            ->setBody($body, 'text/html');
        ;

        $this->mailer->send($message);

        return $message;
    }
}