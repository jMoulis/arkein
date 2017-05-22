<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Ticket;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Ticket controller.
 *
 * @Route("ticket")
 */
class TicketController extends Controller
{
    const ADMIN = 'ROLE_ADMIN';
    /**
     * Lists all ticket entities.
     *
     * @Route("/", name="ticket_index")
     */
    public function indexAction(Request $request)
    {

        return $this->render('ticket/index.html.twig');
    }

    /**
     * Creates a new ticket entity.
     *
     * @Route("/new", name="ticket_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ticket = new Ticket();

        $form = $this->createForm('AppBundle\Form\TicketType', $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ticket->setFromWho($this->getUser());

            // The admin can select which coach can receive the message
            $toWho = $form->getData()->getToWho();

            if (!$toWho) {
                if(!$this->getUser()->getRole() !== 'ROLE_ADMIN')
                {
                    // Fetch the staff coach of the youngster used in the ticket
                    $coach = $em->getRepository('UserBundle:User')->findYoungStaffCoach($ticket->getAboutWho());

                    // If there's no coach assign it to the admin by default
                    if(!$coach){
                        $ticket->setToWho($em->getRepository('UserBundle:User')->findOneBy([
                            'role' => self::ADMIN
                        ]));
                    } else {
                        $ticket->setToWho($coach[0]);
                    }
                }
            }

            $em->persist($ticket);
            $em->flush();

            return $this->redirectToRoute('ticket_show', array('id' => $ticket->getId()));
        }

        return $this->render('ticket/new.html.twig', array(
            'ticket' => $ticket,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ticket entity.
     *
     * @Route("/{id}/show", name="ticket_show")
     * @Method("GET")
     */
    public function showAction(Ticket $ticket, Request $request = null)
    {
        return $this->render('ticket/show.html.twig', array(
            'ticket' => $ticket
        ));
    }

    /**
     * Deletes a ticket entity.
     *
     * @Route("/{id}", name="ticket_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Ticket $ticket)
    {
        $form = $this->createDeleteForm($ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ticket);
            $em->flush();
        }

        return $this->redirectToRoute('ticket_index');
    }

    /**
     * Creates a form to delete a ticket entity.
     *
     * @param Ticket $ticket The ticket entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Ticket $ticket)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ticket_delete', array('id' => $ticket->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
