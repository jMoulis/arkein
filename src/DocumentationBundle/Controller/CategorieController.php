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
 * @Route("categorie")
 */

class CategorieController extends BaseController
{
    /**
     * Lists all document entities.
     *
     * @Route("/", name="categorie_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('DocumentationBundle:Categorie')->findAll();

        $form = $this->createForm('DocumentationBundle\Form\CategorieType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush($categorie);

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('document/categorie/index.html.twig', array(
            'categories' => $categories,
            'form' => $form->createView(),
        ));
    }

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
