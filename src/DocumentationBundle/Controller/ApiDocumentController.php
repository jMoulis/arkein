<?php

namespace DocumentationBundle\Controller;

use AppBundle\Api\DocumentApiModel;
use AppBundle\Controller\BaseController;
use DocumentationBundle\Entity\Categorie;
use DocumentationBundle\Entity\Document;
use DocumentationBundle\Form\Type\DocumentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("document")
 */

class ApiDocumentController extends BaseController
{
    const REPERTOIRE =  'Sites/arkein/documents';
    /**
     * @Route("/api/d/{userid}",
     *     name="api_document_list_by_destinataire",
     *     options={"expose" = true}
     *     )
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     *
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
     * @Route("/api/d/get/{categorie}/c",
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
     * @Route("/api/d/load/{id}/d", name="api_new_doc", options={"expose" = true})
     * @Method({"GET","POST"})
     */
    public  function newAction(Request $request, $id)
    {
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
        $response->headers->set(
            'Location',
            $this->generateUrl('api_document_show', ['id' => $document->getId()])
        );

        return $response;

    }

    /**
     * @Route("/api/d/show/{id}/d", name="api_document_show")
     * @Method("GET")
     */
    public function showAction(Document $document)
    {
        $apiModel = $this->createDocumentApiModel($document);

        return $this->createApiResponse($apiModel);
    }

    /**
     * @Route("/api/d/edit/d", name="api_document_edit", options={"expose" = true})
     * @Method("POST")
     */
    public function editDocumentAction(Request $request)
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
     * @Route("/api/d/delete/{id}/d",
     *     name="api_document_delete",
     *     options={"expose" = true})
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteDocumentAction(Document $document)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($document);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * @Route("/test/", name="test_show_doc")
     */
    public function testShowDocAction()
    {
        return $this->render('document/test_show_doc.html.twig');
    }

    /**
     * @Route("/api/check/",
     *     name="api_check",
     *     options={"expose" = true}
     *  )
     */
    public function downloadImageAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $id = $request->query->get('document');

        $document = $this->getDoctrine()->getRepository('DocumentationBundle:Document')->find($id);
        $nomDocument = $document->getFileName();

        $this->envoiFichier($nomDocument, false);

    }

    private function envoiFichier(Request $request, $documentName, $download = FALSE)
    {
        $document = $this->getParameter('repertoire_documents').'/'.$documentName;

        $mime = $this->getMimeType($request, $document);
        header('Content-type: ' . $mime);
        if($download) {
            header('Content-Disposition: attachement; filename="'. $documentName .'"');
        }

        readfile($document);
    }

    private function getMimeType(Request $request, $document = '')
    {
        if (empty($document))
        {
            throw new BadRequestHttpException('Paramètre invalide');
        }
        if(function_exists('finfo_open'))
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $retour = finfo_file($finfo, $document);
            finfo_close($finfo);
        } elseif (file_exists('mime.ini')){
            $retour = $this->typeMime($document, $request);
        } else {
            $retour = mime_content_type($document);
        }
        return $retour;
    }

    private function typeMime($documentName, Request $request)
    {
        if(preg_match("@Opera(/| )([0-9].[0-9]{1,2})@", $request->server['HTTP_USER_AGENT'], $resultats))
            $navigateur="Opera";
        elseif(preg_match("@MSIE ([0-9].[0-9]{1,2})@", $request->server['HTTP_USER_AGENT'], $resultats))
            $navigateur="Internet Explorer";
        else $navigateur="Mozilla";

        $mime = parse_ini_file("mime.ini");
        $extension = substr($documentName, strrpos($documentName, ".")+1);

        if(array_key_exists($extension, $mime)){
            $type = $mime[$extension];
        }
        else{
            $type = ($navigateur!="Mozilla") ? 'application/octetstream' : 'application/octet-stream';
        }
        return $type;
    }

    /**
     * @param Document $document
     * @return DocumentApiModel
     * @Security("has_role('ROLE_ADMIN')")
     */
    private function createDocumentApiModel(Document $document)
    {
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($document, 'fileTemporary');

        $model = new DocumentApiModel();
        $model->id = $document->getId();
        $model->fileName = $document->getFileName();
        $model->fileTemporary = $path;
        $model->categories = $document->getCategorie()->getId();
        $selfUrl = $this->generateUrl(
            'api_document_show',
            ['id' => $document->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }

}
