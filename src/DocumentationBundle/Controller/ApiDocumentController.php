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
     * @Route("api/docs", name="api_document_list", options={"expose" = true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $documents = $this->getDoctrine()->getRepository('DocumentationBundle:Document')
            ->findAll();

        $models = [];
        foreach ($documents as $document) {
            $models[] = $this->createDocumentApiModel($document);
        }

        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * Creates a new document entity.
     *
     * @Route("/new/{user}/", name="document_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, User $user)
    {
        $form = $this->createForm('DocumentationBundle\Form\DocumentType');
        $form->handleRequest($request);
        $categories = $this->getDoctrine()->getRepository('DocumentationBundle:Categorie')->getCategoryDocumentByUserDisplayed($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $document = $form->getData();
            $document->setAuthor($this->getUser());
            $document->setDestinataire($user);
            $em->persist($document);
            $em->flush($document);

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }
        $documents = $this->getDoctrine()->getRepository('DocumentationBundle:Document')->findBy([
            'destinataire' => $user
        ]);

        return $this->render('document/_doc_index.html.twig', array(
            'categories' => $categories,
            'user' => $user,
            'documents' => $documents,
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("api/doc/{id}", name="api_document_show")
     * @Method("GET")
     */
    public function showAction(Document $document)
    {
        $apiModel = $this->createDocumentApiModel($document);

        return $this->createApiResponse($apiModel);
    }

    /**
     * @Route("api/doc/edit", name="api_document_edit", options={"expose" = true})
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
     * @Route("api/doc/{id}", name="api_document_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
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

        $selfUrl = $this->generateUrl(
            'doc_show',
            ['id' => $document->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }

}
