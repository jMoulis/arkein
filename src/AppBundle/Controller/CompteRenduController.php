<?php

namespace AppBundle\Controller;

use AppBundle\Api\CompteRenduApiModel;
use AppBundle\Entity\CompteRendu;
use AppBundle\Form\Type\CompteRenduType;
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

        $form = $this->get('app.api_response')->ajaxResponse(CompteRenduType::class, $data);

        /** @var compterendu $compterendu */
        $compterendu = $form->getData();

        $pathLoad = $this->pdfProcess($data, $compterendu);

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
        $this->get('app.api_response')->ajaxResponse(CompteRenduType::class, $data);

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

    private function pdfProcess($data, $entity)
    {
        $post_editor = trim($data['compteRendu']);
        $fileName = $entity->getEntretien()->getId();
        $pathSave = "../web/pdf/".$fileName.".pdf";
        $pathLoad = "/pdf/".$fileName.".pdf";

        if(!$this->exportPDF($post_editor,$pathSave))
        {
            throw new BadRequestHttpException('Fichier pdf non sauvegardé');
        }
        return $pathLoad;
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
