<?php

namespace AppBundle\Controller;

use AppBundle\Api\TicketApiModel;
use AppBundle\Entity\Ticket;
use AppBundle\Form\Type\TicketEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("ticket")
 */

class ApiTicketController extends BaseController
{


    /**
     * @Route("api/tickets/created",
     *     name="api_ticket_created_list",
     *     options={"expose" = true})
     * @Method("GET")
     */
    public function ticketCreatedAction()
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
    public function ticketAttributeAction()
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

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(TicketEditType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);

        $em = $this->getDoctrine()->getManager();
        $ticket = $em->getRepository('AppBundle:Ticket')->find($id);
        $ticket->setStatut($data['statut']);

        $em->persist($ticket);
        $em->flush();

        $apiModel = $this->createTicketApiModel($ticket);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
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
