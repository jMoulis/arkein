<?php

namespace DocumentationBundle\Controller;

use AppBundle\Api\RepLogApiModel;
use AppBundle\Controller\BaseController;
use DocumentationBundle\Entity\Categorie;
use DocumentationBundle\Entity\Document;
use DocumentationBundle\Form\DocumentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use UserBundle\Entity\User;

/**
 * @Route("document")
 */

class ApiDocumentController extends BaseController
{

    /**
     * @Route("/docs", name="document_list", options={"expose" = true})
     * @Method("GET")
     */
    public function getDocumentsAction()
    {
        $documents = $this->getDoctrine()->getRepository('DocumentationBundle:Document')
        ->findAll()
        ;

        $models = [];
        foreach ($documents as $document) {
            $models[] = $this->createRepLogApiModel($document);
        }

        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("/docs/{id}", name="doc_get")
     * @Method("GET")
     */
    public function getRepLogAction(Document $document)
    {
        $apiModel = $this->createRepLogApiModel($document);

        return $this->createApiResponse($apiModel);
    }

    /**
     * @Route("/docs/{id}", name="document_delete")
     * @Method("DELETE")
     */
    public function deleteRepLogAction(Document $document)
    {
        /*$this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');*/
        $em = $this->getDoctrine()->getManager();
        $em->remove($document);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * @Route("/docs", name="api_document_new")
     * @Method("POST")
     */
    public function newRepLogAction(Request $request)
    {

        dump($request->getContent());

       /* $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');*/
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        $form = $this->createForm(DocumentType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        /** @var Document $document */
        $document = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $document->setAuthor($this->getUser());
        $document->setDestinataire($this->getUser());

        $em->persist($document);
        $em->flush();

        $apiModel = $this->createRepLogApiModel($document);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
            'Location',
            $this->generateUrl('doc_get', ['id' => $document->getId()])
        );

        return $response;
    }

    /**
     * Turns a RepLog into a RepLogApiModel for the API.
     *
     * This could be moved into a service if it needed to be
     * re-used elsewhere.
     *
     * @param Document $document
     * @return RepLogApiModel
     */
    private function createRepLogApiModel(Document $document)
    {

        $model = new RepLogApiModel();
        $model->id = $document->getId();
        $model->fileName = $document->getFileName();
        $model->fileTemporary = $document->getFileTemporary();

        $selfUrl = $this->generateUrl(
            'doc_get',
            ['id' => $document->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }

}
