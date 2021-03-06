<?php

namespace AppBundle\Controller;

use AppBundle\Api\CompteRenduApiModel;
use AppBundle\Entity\CompteRendu;
use AppBundle\Entity\Entretien;
use AppBundle\Form\Type\CompteRenduType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Form;
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
        $em = $this->getDoctrine()->getManager();

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(CompteRenduType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);

        $this->apiValidFormAction($form);

        $compterendu = $form->getData();

        $pathLoad = $this->pdfProcess($data, $compterendu);
        $compterendu->setLienpdf($pathLoad);

        $entretien = $em->getRepository(Entretien::class)->find($data["entretien"]);
        $entretien->setIsArchived(true);

        $em->persist($compterendu);
        $em->persist($entretien);
        $em->flush();

        $apiModel = $this->createCompteRenduApiModel($compterendu);

        $response = $this->createApiResponseAction($apiModel);
        $response->headers->set(
            'Location',
            $this->generateUrl('compterendu_show', ['id' => $compterendu->getId()])
        );

        return $response;
    }

    private function apiValidFormAction(Form $form)
    {
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromFormAction($form);

            return $this->createApiResponseAction([
                'errors' => $errors
            ], 400);
        }
        return true;
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
        return $this->createApiResponseAction([
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
    public function editAction(Request $request, CompteRendu $compteRendu)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(CompteRenduType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);
        $this->apiValidFormAction($form);

        $em = $this->getDoctrine()->getManager();

        $date = new \DateTime(($data['date']));

        $compteRendu->setObjet($data['objet']);
        $compteRendu->setOdj($data['odj']);
        $compteRendu->setDate($date);

        $em->flush();

        $apiModel = $this->createcompterenduApiModel($compteRendu);

        $response = $this->createApiResponseAction($apiModel);
        $response->headers->set(
            'Location',
            $this->generateUrl('compterendu_show', ['id' => $compteRendu->getId()])
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
        $model->lienPdf = $compterendu->getLienpdf();
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
        $pathSave = "/arkein/web/pdf/".$fileName.".pdf";
        $pathLoad = "../web/pdf/".$fileName.".pdf";

        if(!$this->exportPDF($post_editor,$pathLoad))
        {
            throw new BadRequestHttpException('Fichier pdf non sauvegardé');
        }
        return $pathSave;
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
