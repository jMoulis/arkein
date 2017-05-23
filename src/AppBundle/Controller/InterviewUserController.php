<?php

namespace AppBundle\Controller;

use AppBundle\Api\InterviewUserApiModel;
use AppBundle\Entity\InterviewUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interviewuser controller.
 *
 * @Route("interviewuser")
 */
class InterviewUserController extends BaseController
{
    /**
     * Displays a form to edit an existing $interviewUser entity.
     *
     * @Route("api/{id}/edit", name="api_interviewuser_edit",
     *     options={"expose" = true})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        $entretienId = $request->attributes->get('id');
        $data = json_decode($request->getContent(), true);

        $this->get('app.api_response')->ajaxResponse(InterviewUser::class, $data);

        /** @var InterviewUser $interviewUser */
        $em = $this->getDoctrine()->getManager();
        $entretien = $em->getRepository('AppBundle:Entretien')->find($entretienId);

        $interviewUser = $em->getRepository('AppBundle:InterviewUser')->findOneBy([
            'interview' => $entretien,
            'user' => $this->getUser()
        ]);
        $interviewUser->setStatus($data['status']);
        $em->persist($interviewUser);
        $em->flush();

        $apiModel = $this->createInterviewUserApiModel($interviewUser);

        $response = $this->createApiResponseAction($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
            'Location',
            $this->generateUrl('entretien_show', ['id' => $interviewUser->getId()])
        );

        return $response;
    }

    private function createInterviewUserApiModel(InterviewUser $interviewUser)
    {
        $model = new InterviewUserApiModel();
        $model->id = $interviewUser->getId();
        $selfUrl = $this->generateUrl(
            'entretien_show',
            ['id' => $interviewUser->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }
}
