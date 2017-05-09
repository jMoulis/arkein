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
     * Lists all $interviewUser entities.
     *
     * @Route("/", name="interviewuser_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $interviewUsers = $em->getRepository('AppBundle:InterviewUser')->findAll();

        return $this->render('interviewuser/index.html.twig', array(
            'interviewUsers' => $interviewUsers,
        ));
    }

    /**
     * Creates a new $interviewUser entity.
     *
     * @Route("/new", name="interviewuser_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $interviewUser = new Interviewuser();
        $form = $this->createForm('AppBundle\Form\InterviewUserType', $interviewUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($interviewUser);
            $em->flush();

            return $this->redirectToRoute('interviewuser_show', array('id' => $interviewUser->getId()));
        }

        return $this->render(':interviewuser:new.html.twig', array(
            '$interviewUser' => $interviewUser,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a $interviewUser entity.
     *
     * @Route("/{id}", name="interviewuser_show")
     * @Method("GET")
     */
    public function showAction(InterviewUser $interviewUser)
    {
        $deleteForm = $this->createDeleteForm($interviewUser);

        return $this->render('interviewuser/show.html.twig', array(
            '$interviewUser' => $interviewUser,
            'delete_form' => $deleteForm->createView(),
        ));
    }

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

    /**
     * Deletes a $interviewUser entity.
     *
     * @Route("/{id}", name="interviewuser_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, InterviewUser $interviewUser)
    {
        $form = $this->createDeleteForm($interviewUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($interviewUser);
            $em->flush();
        }

        return $this->redirectToRoute('interviewuser_index');
    }

    /**
     * Creates a form to delete a $interviewUser entity.
     *
     * @param InterviewUser $interviewUser The $interviewUser entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(InterviewUser $interviewUser)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('interviewuser_delete', array('id' => $interviewUser->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
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
