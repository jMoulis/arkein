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

    private $container;
    /**
     * @var TokenStorage
     */

    public function __construct(Session $session, ContainerInterface $container)
    {

        $this->session = $session;
        $this->container = $container;
    }

    public function prePersist()
    {
        $this->session->getFlashBag()->clear();
        return $this->session->getFlashBag()->add('success', $this->container->getParameter('new_message_success'));
    }

    public function preUpdate()
    {
            $this->session->getFlashBag()->clear();
            return $this->session->getFlashBag()->add('success',
                $this->container->getParameter('update_message_success'));
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }
}
