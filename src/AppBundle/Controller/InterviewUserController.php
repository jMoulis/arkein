<?php

namespace AppBundle\Controller;

use AppBundle\Api\InterviewUserApiModel;
use AppBundle\Entity\Entretien;
use AppBundle\Entity\InterviewUser;
use AppBundle\Form\InterviewUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(InterviewUserType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);

        if (!$form->isValid()) {

            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

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

        $response = $this->createApiResponse($apiModel);
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
