<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UserBundle\Entity\Organization;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Organization controller.
 *
 * @Route("organization")
 */
class OrganizationController extends Controller
{
    /**
     * Lists all organization entities.
     *
     * @Route("/", name="organization_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $organizations = $em->getRepository('UserBundle:Organization')->findAll();

        return $this->render('organization/index.html.twig', array(
            'organizations' => $organizations,
        ));
    }

    /**
     * Creates a new organization entity.
     *
     * @Route("/new", name="organization_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $organization = new Organization();
        $form = $this->createForm('UserBundle\Form\OrganizationType', $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush($organization);

            return $this->redirectToRoute('organization_edit', array('id' => $organization->getId()));
        }

        return $this->render('organization/new.html.twig', array(
            'organization' => $organization,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing organization entity.
     *
     * @Route("/{id}/edit", name="organization_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Organization $organization)
    {
        $em = $this->getDoctrine()->getManager();

        $roles = $organization->getCredential()->getRoles();
        $deleteForm = $this->createDeleteForm($organization);
        $editForm = $this->createForm('UserBundle\Form\OrganizationEditType', $organization);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('organization_edit', array('id' => $organization->getId()));
        }

        return $this->render('organization/edit.html.twig', array(
            'roles' => $roles,
            'organization' => $organization,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a organization entity.
     *
     * @Route("/{id}", name="organization_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Organization $organization)
    {
        $form = $this->createDeleteForm($organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($organization);
            $em->flush($organization);
        }

        return $this->redirectToRoute('organization_index');
    }

    /**
     * Creates a form to delete a member entity.
     *
     * @param Organization $organization The member entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Organization $organization)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('organization_delete', array('id' => $organization->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
