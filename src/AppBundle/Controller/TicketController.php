<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Ticket;
use AppBundle\Form\AnswerType;
use AppBundle\Form\TicketType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ticket controller.
 *
 * @Route("ticket")
 */
class TicketController extends Controller
{
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
     * Lists all ticket entities.
     *
     * @Route("/2", name="ticket_index_2")
     */
    public function index2Action(Request $request)
    {
        return $this->render('ticket/_my_attribution.html.twig');
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

            /* TODO: Find the coach and setitTowho */
            //$ticket->getAboutWho()->getCoach();
            $youngster = $ticket->getAboutWho();
            $coaches = $youngster->getCoach();
            $staffCoach = '';
            foreach ($coaches as $coach){
                if($coach->getRole() == 'ROLE_STAFF')
                {
                    $staffCoach = $coach;
                }
            }

            $ticket->setToWho($staffCoach);
            $em->persist($ticket);
            $em->flush($ticket);

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
     * @Route("/{id}/show", name="ticket_show", options = { "expose" = true })
     * @Method("GET")
     */
    public function showAction(Ticket $ticket)
    {
        return $this->render('ticket/show.html.twig', array(
            'ticket' => $ticket
        ));
    }

    /**
     * Displays a form to edit an existing ticket entity.
     *
     * @Route("/{id}/edit", name="ticket_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('edit', ticket)")
     */
    public function editAction(Request $request, Ticket $ticket)
    {
        $deleteForm = $this->createDeleteForm($ticket);

        $editForm = $this->createForm('AppBundle\Form\TicketType', $ticket);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_edit', array('id' => $ticket->getId()));
        }

        return $this->render('ticket/edit.html.twig', array(
            'ticket' => $ticket,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
            $em->flush($ticket);
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

    /**
     * @Route("answer/{id}/new", name="answer_new")
     * @Method("POST")
     */
    public function newAnswerAction(Request $request, Ticket $ticket)
    {
        $form = $this->createForm(AnswerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Answer $answer */

            $answer = $form->getData();
            $answer->setTicket($ticket);
            $answer->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($answer);
            $em->flush();

            return $this->redirectToRoute('ticket_show', ['id' => $ticket->getId()]);
        }
        return $this->render('ticket/comment_form_error.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    public function addCommentAction(Request $request)
    {
        if ($request->isXmlHttpRequest()){
            $id = $request->get('');

        }
    }

    /**
     * @Route("/json/ticket", name="json_ticket",
     *     options={"expose" = true}
     *     )
     *
     */
    public function jsonGetMyTicket()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $tickets = $em->getRepository('AppBundle:Ticket')
                ->findBy([
                    'fromWho' => $user,
                    'statut' => 1
                ]);
        } else {
            $tickets = $em->getRepository('AppBundle:Ticket')
                ->findBy([
                    'statut' => 1
                ]);
        }



        $listTickets = [];
        $i = 0;

        foreach ($tickets as $ticket)
        {
            $listTickets[$i]['id'] = $ticket->getId();
            $listTickets[$i]['date'] = $ticket->getDate()->format('d/m/Y');
            $listTickets[$i]['auteur'] = $ticket->getFromWho()->__toString();
            $listTickets[$i]['message'] = $ticket->getMessage();
            $listTickets[$i]['commentaire'] = $ticket->getAnswers()->count();
            $listTickets[$i]['niveau'] = $ticket->getLevel();
            
            $i++;
        }

        return new JsonResponse(['data' => $listTickets]);
    }

    /**
     * @Route("/json/ticket_attribution", name="json_ticket_attribution",
     *     options={"expose" = true}
     *     )
     *
     */
    public function jsonGetMyAttributions()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser()->getId();

        $em = $this->getDoctrine()->getManager();

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $tickets = $em->getRepository('AppBundle:Ticket')
                ->findBy([
                    'toWho' => $user,
                    'statut' => 1
                ]);
        } else {
            $tickets = $em->getRepository('AppBundle:Ticket')
                ->findBy([
                    'statut' => 1
                ]);
        }


        $listTickets = [];
        $i = 0;

        foreach ($tickets as $ticket)
        {
            $listTickets[$i]['id'] = $ticket->getId();
            $listTickets[$i]['date'] = $ticket->getDate()->format('d/m/Y');
            $listTickets[$i]['auteur'] = $ticket->getFromWho()->__toString();
            $listTickets[$i]['message'] = $ticket->getMessage();
            $listTickets[$i]['commentaire'] = $ticket->getAnswers()->count();
            $listTickets[$i]['niveau'] = $ticket->getLevel();

            $i++;
        }

        return new JsonResponse(['data' => $listTickets]);
    }
}
