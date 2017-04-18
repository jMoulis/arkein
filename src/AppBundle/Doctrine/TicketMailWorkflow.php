<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 11/03/2017
 * Time: 14:34
 */

namespace AppBundle\Doctrine;

use AppBundle\Entity\Ticket;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Swift_Mailer;

class TicketMailWorkflow implements EventSubscriber
{
    const ADMINMAIL = 'julien.moulis@mac.com';

    private $mailer;
    private $engine;
    private $tokenStorage;

    public function __construct(Swift_Mailer $mailer, EngineInterface $engine, TokenStorageInterface $tokenStorage)
    {
        $this->mailer = $mailer;
        $this->engine = $engine;
        $this->tokenStorage = $tokenStorage;
    }

    public function postPersist(LifecycleEventArgs $args){
        $entity = $args->getEntity();
        if(!$entity instanceof Ticket){
            return;
        }
        $author = $entity->getFromWho()->getEmail();
        $coaches = $entity->getAboutWho()->getCoach();
        $objet = $entity->getObjet();
        $destinataires = self::getGroupMemberMailPerYoung($objet, $coaches);
        $body =
            '<html>' .
            '<head></head>' .
            '<body>' .
            '<p>'. $entity->getMessage() .'</p>'.
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
            ->setSubject("Voici un nouveau ticket: " . $objet)
            ->setFrom($auteur)
            //->setTo($destinataires)
            ->setCc(self::ADMINMAIL)
            ->setBody($body, 'text/html');
        ;

        $this->mailer->send($message);

        return $message;
    }

    //Retrieves emails and creates the object to be used as setTo swiftMailer array
    private function getGroupMemberMailPerYoung($objet, $coaches){
        $destinataires = [];
        foreach ($coaches as $coach){
            foreach ($coach->getGroups() as $group){
                if($group->getName() == $objet){
                    $destinataires[$coach->getEmail()] = $coach->__toString();
                }
            }
        }
        return $destinataires;
    }

}