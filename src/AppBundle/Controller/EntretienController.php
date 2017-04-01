<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entretien;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

/**
 * Entretien controller.
 *
 * @Route("entretien")
 */
class EntretienController extends Controller
{
    /**
     * Lists all entretien entities par interviewee.
     *
     * @Route("/", name="entretien_index")
     * @Method("GET")
     *
     */
    public function indexAction(Request $request, User $user = null)
    {
        $em = $this->getDoctrine()->getManager();
        $userFilter = '';

        if($user == null) {
            $user = $this->getUser();
            $userFilter = 'interviewer';
        } else {
            $userFilter ='interviewee';
        }

        $entretiens = $em->getRepository('AppBundle:Entretien')->findBy([
            $userFilter => $user
        ]);

        return $this->render('entretien/index.html.twig', array(
            'entretiens' => $entretiens,
        ));
    }

    /**
     * Creates a new entretien entity.
     *
     * @Route("/new", name="entretien_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $entretien = new Entretien();
        $form = $this->createForm('AppBundle\Form\EntretienType', $entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entretien);
            $em->flush();

            return $this->redirectToRoute('entretien_show', array('id' => $entretien->getId()));
        }

        return $this->render('entretien/new.html.twig', array(
            'entretien' => $entretien,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a entretien entity.
     *
     * @Route("/{id}", name="entretien_show")
     * @Method("GET")
     */
    public function showAction(Entretien $entretien)
    {
        $deleteForm = $this->createDeleteForm($entretien);

        return $this->render('entretien/show.html.twig', array(
            'entretien' => $entretien,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing entretien entity.
     *
     * @Route("/{id}/edit", name="entretien_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Entretien $entretien)
    {
        $deleteForm = $this->createDeleteForm($entretien);
        $editForm = $this->createForm('AppBundle\Form\EntretienType', $entretien);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('entretien_edit', array('id' => $entretien->getId()));
        }

        return $this->render('entretien/edit.html.twig', array(
            'entretien' => $entretien,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a entretien entity.
     *
     * @Route("/{id}", name="entretien_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Entretien $entretien)
    {
        $form = $this->createDeleteForm($entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entretien);
            $em->flush();
        }

        return $this->redirectToRoute('entretien_index');
    }

    /**
     * Creates a form to delete a entretien entity.
     *
     * @param Entretien $entretien The entretien entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Entretien $entretien)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('entretien_delete', array('id' => $entretien->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
