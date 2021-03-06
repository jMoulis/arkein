<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 11/03/2017
 * Time: 14:34
 */

namespace AppBundle\Doctrine;


use AppBundle\Entity\Entretien;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Swift_Mailer;

class EntretienMailWorkflow implements EventSubscriber
{
    const NOUVELENTRETIEN= "Nouvel Entretien";
    const ADMINMAIL = 'julien.moulis@moulis.me';


    private $mailer;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args){
        $entity = $args->getEntity();
        if(!$entity instanceof Entretien){
            return;
        }

        $author = $entity->getAuthor()->getEmail();
        $guests = $entity->getInterviewGuests();
        $objet = $entity->getObjet();

        $destinataires = self::getGroupMemberMailPerYoung($guests);
        $body =
            '<html>' .
            '<head></head>' .
            '<body>' .
            '<p>'. $entity->getCompteRendu() .'</p>'.
            '</body>' .
            '</html>';
        $this->sendEmail($objet, $author, $destinataires, $body);
    }

    public function getSubscribedEvents()
    {
        return ['postPersist'];
    }

    private function sendEmail($objet, $auteur, $destinataires, $body)
    {
        $message = $this->mailer->createMessage()
            ->setSubject("Invitation à un entretien: " . $objet)
            ->setFrom($auteur)
            ->setTo($destinataires)
            ->setCc(self::ADMINMAIL)
            ->setBody($body, 'text/html');
        ;

        $this->mailer->send($message);

        return $message;
    }

    //Retrieves emails and creates the object to be used as setTo swiftMailer array
    private function getGroupMemberMailPerYoung($guests){
        $destinataires = [];

        foreach ($guests as $guest){
            $destinataires[$guest->getUser()->getEmail()] = $guest->getUser()->getFullName();
        }
        return $destinataires;
    }
}
