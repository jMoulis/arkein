<?php

namespace AppBundle\Controller;

use AppBundle\Api\CompteRenduApiModel;
use AppBundle\Entity\CompteRendu;
use AppBundle\Form\Type\CompteRenduType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use mPDF;

/**
 * compterendu controller.
 *
 * @Route("compterendu")
 */
class CompteRenduController extends BaseController
{

    /**
     * @Route("/api/new/compterendu/",
     *     name="compterendu_new",
     *     options={"expose" = true})
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(CompteRenduType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);

        if (!$form->isValid()) {

            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        /** @var compterendu $compterendu */
        $compterendu = $form->getData();

        $post_editor = trim($data['compteRendu']);
        $fileName = $compterendu->getEntretien()->getId();
        $pathSave = "../web/pdf/".$fileName.".pdf";
        $pathLoad = "/pdf/".$fileName.".pdf";

        if(!$this->exportPDF($post_editor,$pathSave))
        {
            throw new BadRequestHttpException('Fichier pdf non sauvegardé');
        }

        $em = $this->getDoctrine()->getManager();
        $compterendu->setLienpdf($pathLoad);

        $em->persist($compterendu);
        $em->flush();

        $apiModel = $this->createCompteRenduApiModel($compterendu);

        $response = $this->createApiResponse($apiModel);
        $response->headers->set(
            'Location',
            $this->generateUrl('compterendu_show', ['id' => $compterendu->getId()])
        );

        return $response;
    }

    /**
     * Finds and displays a compterendu entity.
     *
     * @Route("/api/modal/{id}/show",
     *     name="compterendu_modal_detail",
     *     options={"expose" = true})
     * @Method("GET")
     */
    public function detailModalAction(CompteRendu $compterendu)
    {
        if(!$compterendu) {
            throw new \Exception('erreur object non trouvé', 500);
        }
        $compterendu = $this->getDoctrine()->getRepository('AppBundle:CompteRendu')
            ->find($compterendu->getId())
        ;
        $model = $this->createcompterenduApiModel($compterendu);
        return $this->createApiResponse([
            'item' => $model
        ]);
    }

    /**
     * Finds and displays a compterendu entity.
     *
     * @Route("/{id}/show",
     *     name="compterendu_show",
     *     options={"expose" = true})
     * @Method("GET")
     */
    public function showAction(CompteRendu $compteRendu)
    {
        return $this->render('compterendu/show.html.twig', array(
            'compterendu' => $compteRendu
        ));
    }

    /**
     * Displays a form to edit an existing compterendu entity.
     *
     * @Route("api/compterendu/{id}/edit", name="compterendu_edit",
     *     options={"expose" = true})
     * @Method({"GET", "POST"})
     *
     */
    public function editAction(Request $request, $id)
    {

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(compterenduType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);

        if (!$form->isValid()) {

            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        $em = $this->getDoctrine()->getManager();
        $compterendu = $em->getRepository('AppBundle:compterendu')->findOneBy(['id' => $id]);

        $date = new \DateTime(($data['date']));

        $compterendu->setObjet($data['objet']);
        $compterendu->setOdj($data['odj']);
        $compterendu->setDate($date);

        $em->persist($compterendu);
        $em->flush();

        $apiModel = $this->createcompterenduApiModel($compterendu);

        $response = $this->createApiResponse($apiModel);
        $response->headers->set(
            'Location',
            $this->generateUrl('compterendu_show', ['id' => $compterendu->getId()])
        );

        return $response;

    }

    /**
     * Deletes a compterendu entity.
     *
     * @Route("/{id}", name="compterendu_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, compterendu $compterendu)
    {
        $form = $this->createDeleteForm($compterendu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($compterendu);
            $em->flush();
        }

        return $this->redirectToRoute('compterendu_index');
    }

    /**
     * Creates a form to delete a compterendu entity.
     *
     * @param compterendu $compterendu The compterendu entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(compterendu $compterendu)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('compterendu_delete', array('id' => $compterendu->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function createCompteRenduApiModel(CompteRendu $compterendu)
    {
        $model = new CompteRenduApiModel();
        $model->id = $compterendu->getId();
        $model->date = $compterendu->getDate()->format('d/m/Y');
        $model->compteRendu = $compterendu->getCompteRendu();
        $model->presence = $compterendu->getPresence();
        $model->entretien = $compterendu->getEntretien()->getId();
        $selfUrl = $this->generateUrl(
            'compterendu_show',
            ['id' => $compterendu->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }

    private function exportPDF($fileName, $path)
    {
        try
        {
            $pdf = new mPDF();
            $pdf->WriteHTML($fileName);
            $pdf->Output($path,'F');
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

}
