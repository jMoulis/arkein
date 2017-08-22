<?php

namespace AppBundle\Controller;

use AppBundle\Api\TicketApiModel;
use AppBundle\Entity\Ticket;
use AppBundle\Form\Type\TicketEditType;
use AppBundle\Form\Type\TicketType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("ticket")
 */

class TicketController extends BaseController
{
    const ADMIN = 'ROLE_ADMIN';

    /**
     * Lists all ticket entities.
     *
     * @Route("/", name="ticket_index")
     */
    public function indexAction()
    {
        return $this->render('ticket/index.html.twig');
    }

    /**
     * Creates a new ticket entity.
     *
     * @Route("/new", name="api_ticket_new", options={"expose" = true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(TicketType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);

        $this->apiValidFormAction($form);

        $ticket = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $ticket->setFromWho($this->getUser());

        $toWho = $form->getData()->getToWho();

        //If toWho is not selected by admin then we find the coach
        $this->toWhoAction($toWho, $em, $ticket);

        $em->persist($ticket);
        $em->flush();

        $apiModel = $this->createTicketApiModel($ticket);

        $response = $this->createApiResponseAction($apiModel);
        $response->headers->set(
            'Location',
            $this->generateUrl('ticket_show', ['id' => $ticket->getId()])
        );

        return $response;
    }

    /**
     * Finds and displays a ticket entity.
     *
     * @Route("/{id}/show", name="ticket_show")
     * @Method("GET")
     */
    public function showAction(Ticket $ticket)
    {
        return $this->render('ticket/show.html.twig', array(
            'ticket' => $ticket
        ));
    }

    /**
     * @Route("api/tickets/created",
     *     name="api_ticket_created_list",
     *     options={"expose" = true})
     * @Method("GET")
     */
    public function getTicketCreatedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $tickets = $em->getRepository('AppBundle:Ticket')->getAllTicketsUserFromAndTo($user);

        $models = [];
        foreach ($tickets as $ticket) {
            $models[] = $this->createTicketApiModel($ticket);
        }
        return $this->createApiResponseAction([
            'items' => $models
        ]);
    }

    /**
     * @Route("api/ticket/{id}/edit",
     *     name="api_ticket_edit",
     *     options={"expose" = true})
     * @Method("POST")
     */
    public function editAction(Request $request, Ticket $ticket)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(TicketEditType::class, $ticket, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);

        $this->apiValidFormAction($form);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $apiModel = $this->createTicketApiModel($ticket);

        $response = $this->createApiResponseAction($apiModel);
        $response->headers->set(
            'Location',
            $this->generateUrl('ticket_show', ['id' => $ticket->getId()])
        );

        return $response;
    }

    /**
     * @Route("api/ticket/{id}", name="api_ticket_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteTicketAction(Ticket $ticket)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($ticket);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * Used to fetch the coach
     */
    private function toWhoAction($toWho, $em, Ticket $ticket)
    {
        if (!$toWho) {
            if (!$this->getUser()->getRole() !== 'ROLE_ADMIN') {
                // Fetch the staff coach of the youngster used in the ticket
                $coach = $em->getRepository('UserBundle:User')->findYoungStaffCoach($ticket->getAboutWho());

                // If there's no coach assigned it to the admin by default
                if (!$coach) {
                    $ticket->setToWho($em->getRepository('UserBundle:User')->findOneBy([
                        'role' => self::ADMIN
                    ]));
                } else {
                    $ticket->setToWho($coach[0]);
                }
            }
            if ($this->getUser()->getRole() === 'ROLE_YOUNGSTER') {
                $ticket->setAboutWho($this->getUser());
            }
        }
    }

    private function apiValidFormAction(Form $form)
    {
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromFormAction($form);

            return $this->createApiResponseAction([
                'errors' => $errors
            ], 400);
        }
        return true;
    }

    /**
     * @param Ticket $ticket
     * @return TicketApiModel
     */
    private function createTicketApiModel(Ticket $ticket)
    {
        $model = new TicketApiModel();

        $model->id = $ticket->getId();
        $model->date = $ticket->getDate()->format('d-M-y');
        $model->message = $ticket->getMessage();
        $model->auteur = $ticket->getFromWho()->getFullName();
        $model->auteurId = $ticket->getFromWho()->getId();
        $model->reponses = count($ticket->getAnswers());
        $model->destinataire = $ticket->getToWho()->getFullName();
        $model->niveau = $ticket->getLevel();
        $model->statut = $ticket->getStatut();
        $model->titre = $ticket->getFromWho()->getTitre();
        $model->auteurEmail = $ticket->getFromWho()->getEmail();
        $selfUrl = $this->generateUrl(
            'ticket_show',
            ['id' => $ticket->getId()]
        );
        $archivedUrl = $this->generateUrl(
            'api_ticket_edit',
            ['id' => $ticket->getId()]
        );
        $model->addLink('_self', $selfUrl);
        $model->addLink('_archived', $archivedUrl);

        return $model;
    }

}
