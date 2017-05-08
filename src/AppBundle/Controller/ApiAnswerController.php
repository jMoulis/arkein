<?php

namespace AppBundle\Controller;

use AppBundle\Api\AnswerApiModel;
use AppBundle\Entity\Answer;
use AppBundle\Entity\Ticket;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("answer")
 */

class ApiAnswerController extends BaseController
{

    /**
     * @Route("api/answers/list", name="api_answer_list", options={"expose" = true})
     * @Method("GET")
     */
    public function indexAction()
    {
        /*TODO Find the ticket number to display the right answers*/
        $em = $this->getDoctrine()->getManager();
        $answers = $em->getRepository('AppBundle:Answer')->findBy([
            'ticket' => 230
        ]);

        $models = [];

        foreach ($answers as $answer) {
            $models[] = $this->createAnswerApiModel($answer);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("api/answers/new/", name="api_answer_new", options={"expose" = true})
     * @Method("POST")
     */
    public function newAnswerAction(Request $request)
    {

        /* $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');*/
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(Answer::class, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        /** @var Answer $answer */
        $answer = $form->getData();
        $em = $this->getDoctrine()->getManager();

        $em->persist($answer);
        $em->flush();

        $apiModel = $this->createAnswerApiModel($answer);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
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
        $model->auteur = $answer->getUser()->__toString();
        return $model;
    }

}
