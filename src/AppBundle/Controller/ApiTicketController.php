<?php

namespace AppBundle\Controller;

use AppBundle\Api\TicketApiModel;
use AppBundle\Entity\Ticket;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("ticket")
 */

class ApiTicketController extends BaseController
{

    /**
     * @Route("api/tickets/created", name="api_ticket_created_list", options={"expose" = true})
     * @Method("GET")
     */
    public function ticketCreatedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($user->getRole() !== 'ROLE_ADMIN')
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
        $models = [];
        foreach ($tickets as $ticket) {
            $models[] = $this->createTicketApiModel($ticket);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("api/tickets/attributed", name="api_ticket_attributed_list", options={"expose" = true})
     * @Method("GET")
     */
    public function ticketAttributeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($user->getRole() !== 'ROLE_ADMIN')
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
        $models = [];
        foreach ($tickets as $ticket) {
            $models[] = $this->createTicketApiModel($ticket);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }



    /**
     * @Route("api/ticket/edit", name="api_ticket_edit", options={"expose" = true})
     * @Method("POST")
     */
    public function editAction(Request $request)
    {
        $content = $request->getContent();
        if ($content === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        if($request->isXmlHttpRequest()){

            if(!empty($content))
            {
                $em = $this->getDoctrine()->getManager();
                $params = json_decode($content, true);
                $ticket = $em->getRepository('AppBundle:Ticket')->findOneBy(['id' => $params['id']]);
                $ticket->setStatut($params['statut']);
                dump($params);
                $em->persist($ticket);
                $em->flush();
            }
            return new JsonResponse(['data' => $params]);
        }
        return new Response("Error", 400);
    }

    /**
     * @Route("api/ticket/{id}", name="api_ticket_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteTicketAction(Ticket $ticket)
    {
        /*$this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');*/
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
        $model->auteur = $ticket->getFromWho()->__toString();
        $model->reponses = count($ticket->getAnswers());

        $model->niveau = $ticket->getLevel();

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
