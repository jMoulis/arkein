<?php

namespace AppBundle\Controller;

use AppBundle\Api\DocumentApiModel;
use AppBundle\Entity\Mail;
use DocumentationBundle\Entity\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class AppController extends BaseController
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render(':App:index.html.twig', array(
        ));
    }

    /**
     *
     * @Route("/dashboard", name="dashboard")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function dashboardAction()
    {
        return $this->render(':dashboard:dashboard.html.twig');

    }

    /**
     * @Route("/contact", name="contact")
     * @Method({"GET","POST"})
     */
    public function contactAction(Request $request)
    {
        $mail = new Mail();
        $form = $this->createForm('AppBundle\Form\MailType', $mail);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($mail);
            $em->flush();
            $message = \Swift_Message::newInstance()
                ->setSubject($mail->getObjet())
                ->setFrom($mail->getMail())
                ->setTo('julien.moulis@moulis.me')
                ->setBody(
                    $this->renderView(':email:_template_contact.html.twig', [
                        'mail' => $mail
                    ]),
                    'text/html'
                );
            $this->get('mailer')->send($message);

            return $this->redirectToRoute('app_app_index');
        }

        return $this->render(':App:contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     *
     * @Route("/events", name="event")
     */
    public function eventAction()
    {
        $events = $this->getDoctrine()->getRepository('AppBundle:Event')->findAll();
        return $this->render(':App:event.html.twig', [
            'events' => $events
        ]);

    }
    /**
     * @Route("/filesystem", name="filesystem")
     */
    public function filesystemAction()
    {
        return $this->render(':testFileSystem:index.html.twig');
    }

}
