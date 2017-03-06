<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AppController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render(':App:index.html.twig', array(
        ));
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboardAction()
    {
        return $this->render(':App:dashboard.html.twig');
    }

}
