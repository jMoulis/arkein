<?php

namespace DocumentationBundle\Controller;

use AppBundle\Api\DocumentApiModel;
use AppBundle\Controller\BaseController;
use DocumentationBundle\Entity\Categorie;
use DocumentationBundle\Entity\Document;
use DocumentationBundle\Form\CategorieType;
use DocumentationBundle\Form\DocumentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("api/docs/{userid}", name="api_document_list_by_destinataire", options={"expose" = true})
     * @Method("GET")
     */
    public function indexAction($userid)
    {
        $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($userid);
        $documents = $this->getDoctrine()->getRepository('DocumentationBundle:Document')
            ->getDocumentsByDestinataire($user);

        $models = [];
        foreach ($documents as $document) {
            $models[] = $this->createDocumentApiModel($document);
        }

        return $this->createApiResponse([
            'items' => $models
        ]);
    }


    /**
     * @Route("api/docs/{categorie}",
     *     name="api_document_list",
     *     options={"expose" = true}
     *     )
     * @Method("GET")
     */
    public function getDocsByCategorieAction(Categorie $categorie)
    {
        $em = $this->getDoctrine()->getManager();
        $documents = $this->getDoctrine()->getRepository('DocumentationBundle:Document')
            ->findBy([
                'categorie' => $em->getRepository('DocumentationBundle:Categorie')->find($categorie)
            ]);

        $models = [];
        foreach ($documents as $document) {
            $models[] = $this->createDocumentApiModel($document);
        }

        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("/load/{id}", name="api_new_doc", options={"expose" = true})
     * @Method({"GET","POST"})
     */
    public  function newAction(Request $request, $id)
    {

        /* $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');*/
        $data = $request->files;

        if ($data === null) {
            throw new BadRequestHttpException('Files Empty');
        }
        $form = $this->createForm(DocumentType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        /** @var Document $document */
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($id);
        //
        $document = $form->getData();
        $document->setAuthor($this->getUser());
        $document->setDestinataire($user);

        $em->persist($document);
        $em->flush();

        $apiModel = $this->createDocumentApiModel($document);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
            'Location',
            $this->generateUrl('api_document_show', ['id' => $document->getId()])
        );

        return $response;

    }

    /**
     * @Route("/api/doc/{id}", name="api_document_show")
     * @Method("GET")
     */
    public function showAction(Document $document)
    {
        $apiModel = $this->createDocumentApiModel($document);

        return $this->createApiResponse($apiModel);
    }

    /**
     * @Route("/api/doc/edit", name="api_document_edit", options={"expose" = true})
     * @Method("POST")
     */
    public function editDocument(Request $request)
    {
        $content = $request->getContent();
        if ($content === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        if($request->isXmlHttpRequest()){

            if(!empty($content))
            {
                $em = $this->getDoctrine()->getManager();
                $params = json_decode($content, true);
                $document = $em->getRepository('DocumentationBundle:Document')->findOneBy(['id' => $params['id']]);

                $categorie = $em->getRepository('DocumentationBundle:Categorie')->findOneBy(['id' => $params['idCat']]);

                $document->setCategorie($categorie);
                $em->persist($document);
                $em->flush();
            }
            return new JsonResponse(['data' => $params]);
        }
        return new Response("Error", 400);
    }

    /**
     * @Route("/api/doc/{id}", name="api_document_delete", options={"expose" = true})
     * @Method("DELETE")
     */
    public function deleteDocumentAction(Document $document)
    {
        /*$this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');*/
        $em = $this->getDoctrine()->getManager();
        $em->remove($document);
        $em->flush();

        return new Response(null, 204);
    }


    /**
     * @param Document $document
     * @return DocumentApiModel
     */
    private function createDocumentApiModel(Document $document)
    {

        $model = new DocumentApiModel();
        $model->id = $document->getId();
        $model->fileName = $document->getFileName();
        $model->fileTemporary = $document->getFileTemporary();
        $model->categories = $document->getCategorie()->getId();
        $selfUrl = $this->generateUrl(
            'api_document_show',
            ['id' => $document->getId()]
        );
        $deleteUrl = $this->generateUrl('api_document_delete', ['id' => $document->getId()]);
        $model->addLink('_self', $selfUrl);

        return $model;
    }

}
