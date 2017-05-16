<?php

namespace DocumentationBundle\Controller;

use AppBundle\Api\DocumentApiModel;
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
 * @Route("")
 */

class CategorieController extends BaseController
{
    /**
     * Creates a new document entity.
     *
     * @Route("/new", name="categorie_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm('DocumentationBundle\Form\CategorieType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $categorie = $form->getData();
            $em->persist($categorie);
            $em->flush($categorie);

            return $this->redirectToRoute('categorie_new');
        }
        $categories = $this->getDoctrine()->getRepository('DocumentationBundle:Categorie')->findAll();

        return $this->render('document/categorie/_new.html.twig', array(
            'categories' => $categories,
            'form' => $form->createView(),
        ));
    }


}
