<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Billet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Billet controller.
 *
 * @Route("billet")
 */
class BilletController extends Controller
{
    /**
     * Lists all billet entities.
     *
     * @Route("/", name="billet_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $billets = $em->getRepository('BlogBundle:Billet')->findAll();

        return $this->render('blog/billet/index.html.twig', array(
            'billets' => $billets,
        ));
    }

    /**
     * Creates a new billet entity.
     *
     * @Route("/new", name="billet_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $billet = new Billet();
        $form = $this->createForm('BlogBundle\Form\BilletType', $billet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $billet->setAuthor($this->getUser());
            $em->persist($billet);
            $em->flush();

            return $this->redirectToRoute('billet_show', array('id' => $billet->getId()));
        }

        return $this->render('blog/billet/new.html.twig', array(
            'billet' => $billet,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a billet entity.
     *
     * @Route("/{id}", name="billet_show")
     * @Method("GET")
     */
    public function showAction(Billet $billet)
    {
        $deleteForm = $this->createDeleteForm($billet);

        return $this->render('blog/billet/show.html.twig', array(
            'billet' => $billet,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing billet entity.
     *
     * @Route("/{id}/edit", name="billet_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Billet $billet)
    {
        $deleteForm = $this->createDeleteForm($billet);
        $editForm = $this->createForm('BlogBundle\Form\BilletType', $billet);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('billet_edit', array('id' => $billet->getId()));
        }

        return $this->render('blog/billet/edit.html.twig', array(
            'billet' => $billet,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a billet entity.
     *
     * @Route("/{id}", name="billet_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Billet $billet)
    {
        $form = $this->createDeleteForm($billet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($billet);
            $em->flush();
        }

        return $this->redirectToRoute('billet_index');
    }

    /**
     * Creates a form to delete a billet entity.
     *
     * @param Billet $billet The billet entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Billet $billet)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('billet_delete', array('id' => $billet->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
