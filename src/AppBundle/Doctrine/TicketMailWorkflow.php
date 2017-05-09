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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Swift_Mailer;
use UserBundle\Entity\User;

class TicketMailWorkflow implements EventSubscriber
{
    const ADMINMAIL = 'julien.moulis21@gmail.com';

    private $mailer;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args){
        $entity = $args->getEntity();
        if(!$entity instanceof Ticket){
            return;
        }

        $coaches = $entity->getAboutWho()->getCoach();
        $author = $entity->getFromWho()->getEmail();
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
            ->setTo($destinataires)
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
            // Fetch the coach Staff and set it by default into the array destinataires
            if($coach->getRole() == 'ROLE_STAFF'){
                $destinataires[$coach->getEmail()] = $coach->__toString();
            }
            foreach ($coach->getGroups() as $group){
                // Fetch the coach's group and compare to the object ticket
                if($group->getName() == $objet){
                    $destinataires[$coach->getEmail()] = $coach->__toString();
                }
            }
        }
        return $destinataires;
    }

}