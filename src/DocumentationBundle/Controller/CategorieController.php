<?php

namespace DocumentationBundle\Controller;


use AppBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
        $form = $this->createForm('DocumentationBundle\Form\Type\CategorieType');
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
