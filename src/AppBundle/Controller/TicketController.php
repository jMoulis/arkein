<?php

namespace AppBundle\Controller;

use AppBundle\Api\TicketApiModel;
use AppBundle\Entity\Ticket;
use AppBundle\Form\Type\TicketEditType;
use AppBundle\Form\Type\TicketType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/new", name="ticket_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ticket = new Ticket();

        $form = $this->createForm(TicketType::class, $ticket);
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
        $tickets = $em->getRepository('AppBundle:Ticket')->findBy([
            'fromWho' => $user,
            'statut' => 1
        ]);
        $models = [];
        foreach ($tickets as $ticket) {
            $models[] = $this->createTicketApiModel($ticket);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("api/tickets/attributed",
     *     name="api_ticket_attributed_list",
     *     options={"expose" = true})
     * @Method("GET")
     */
    public function getTicketAttributeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $tickets = $em->getRepository('AppBundle:Ticket')->findBy([
            'toWho' => $user,
            'statut' => 1
        ]);
        $models = [];
        foreach ($tickets as $ticket) {
            $models[] = $this->createTicketApiModel($ticket);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("api/ticket/{id}edit",
     *     name="api_ticket_edit",
     *     options={"expose" = true})
     * @Method("POST")
     */
    public function editAction(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);

        $this->get('app.api_response')->ajaxResponse(TicketEditType::class, $data);

        $em = $this->getDoctrine()->getManager();
        $ticket = $em->getRepository('AppBundle:Ticket')->find($id);
        $ticket->setStatut($data['statut']);

        $em->persist($ticket);
        $em->flush();

        $apiModel = $this->createTicketApiModel($ticket);

        $response = $this->createApiResponse($apiModel);
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
        $model->reponses = count($ticket->getAnswers());
        $model->destinataire = $ticket->getToWho()->getFullName();
        $model->niveau = $ticket->getLevel();
        $model->statut = $ticket->getStatut();

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
