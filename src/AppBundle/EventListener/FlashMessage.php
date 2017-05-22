<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 10/03/2017
 * Time: 12:19
 */

namespace AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class FlashMessage implements EventSubscriber
{

    private $session;


    public function __construct(Session $session)
    {

        $this->session = $session;
    }

    public function prePersist()
    {
        $this->session->getFlashBag()->clear();
        return $this->session->getFlashBag()->add('success', 'Données correctements enregistrées');
    }

    public function preUpdate()
    {
            $this->session->getFlashBag()->clear();
            return $this->session->getFlashBag()->add('success', 'Modifications validées');
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }
}
