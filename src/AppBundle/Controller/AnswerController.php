<?php

namespace AppBundle\Controller;

use AppBundle\Api\AnswerApiModel;
use AppBundle\Entity\Answer;
use AppBundle\Form\Type\AnswerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("answer")
 */

class AnswerController extends BaseController
{

    /**
     * @Route("api/answers/{id}/list", name="api_answer_list", options={"expose" = true})
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $ticketId = $request->attributes->get('id');

        $em = $this->getDoctrine()->getManager();
        $answers = $em->getRepository('AppBundle:Answer')
                ->findAnswerOrderedByDate($em->getRepository('AppBundle:Ticket')
                ->find($ticketId));
        $models = [];
        foreach ($answers as $answer) {
            $models[] = $this->createAnswerApiModel($answer);
        }
        return $this->createApiResponseAction([
            'items' => $models
        ]);
    }

    /**
     * @Route("api/answers/{id}/new/",
     *     name="api_answer_new",
     *     options={"expose" = true})
     * @Method("POST")
     */
    public function newAnswerAction(Request $request)
    {
        $ticketId = $request->attributes->get('id');
        $data = json_decode($request->getContent(), true);
        $form = $this->get('app.api_response')->ajaxResponse(AnswerType::class, $data);

        /** @var Answer $answer */
        $answer = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $answer->setUser($this->getUser());
        $answer->setTicket($em->getRepository('AppBundle:Ticket')->find($ticketId));
        $em->persist($answer);
        $em->flush();

        $apiModel = $this->createAnswerApiModel($answer);

        $response = $this->createApiResponseAction($apiModel);
        $response->headers->set(
            'Location',
            $this->generateUrl('ticket_show', ['id' => $answer->getTicket()->getId()])
        );

        return $response;
    }

    /**
     * @param Answer $answer
     * @return AnswerApiModel
     */
    private function createAnswerApiModel(Answer $answer)
    {
        $model = new AnswerApiModel();
        $model->id = $answer->getId();
        $model->date = $answer->getDateCreated()->format('d-M-y');
        $model->message = $answer->getMessage();
        $model->auteur = $answer->getUser()->getFullName();
        return $model;
    }

}
