<?php

namespace DocumentationBundle\Controller;

use AppBundle\Controller\BaseController;
use DocumentationBundle\Entity\Categorie;
use Metadata\Tests\Driver\Fixture\C\SubDir\C;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

/**
 * @Route("document")
 */

class DocumentController extends BaseController
{
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
}
