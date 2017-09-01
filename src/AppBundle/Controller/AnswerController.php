<?php

namespace AppBundle\Controller;

use AppBundle\Api\AnswerApiModel;
use AppBundle\Entity\Answer;
use AppBundle\Entity\Ticket;
use AppBundle\Form\Type\AnswerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("answer")
 */

class AnswerController extends BaseController
{

    /**
     * @Route("api/answers/{id}/list", name="api_answer_list", options={"expose" = true})
     * @Method("GET")
     */
    public function indexAction(Request $request, Ticket $ticket)
    {
        $em = $this->getDoctrine()->getManager();
        $answers = $em->getRepository('AppBundle:Answer')
                ->findAnswerOrderedByDate($em->getRepository('AppBundle:Ticket')
                ->find($ticket->getId()));
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
    public function newAnswerAction(Request $request, Ticket $ticket)
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(AnswerType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromFormAction($form);

            return $this->createApiResponseAction([
                'errors' => $errors
            ], 400);
        }

        /** @var Answer $answer */
        $answer = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $answer->setUser($this->getUser());
        $answer->setTicket($ticket);
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
        $model->titre = $answer->getUser()->getTitre();
        $model->auteurEmail = $answer->getUser()->getEmail();
        return $model;
    }

}
