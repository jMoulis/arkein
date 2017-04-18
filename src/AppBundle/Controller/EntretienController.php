<?php

namespace AppBundle\Controller;

use AppBundle\Api\EntretienApiModel;
use AppBundle\Entity\Entretien;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

/**
 * Entretien controller.
 *
 * @Route("entretien")
 */
class EntretienController extends BaseController
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

        /* Cette action étant utilisée à deux endroits, il est nécessaire de mettre une condition
        * Afin d'éviter une erreur doctrine lors de la lecture des entretiens sur la liste globale hors fiche membre
        */

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
     * @Route("/new/", name="entretien_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $entretien = new Entretien();
        $form = $this->createForm('AppBundle\Form\EntretienType', $entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entretien->setInterviewer($this->getUser());

            $em->persist($entretien);
            $em->flush();

            return $this->redirectToRoute('user_show', array('id' => $entretien->getInterviewee()->getId()));
        }

        return $this->render('entretien/new.html.twig', array(
            'entretien' => $entretien,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/{id}/",
     *     name="entretien_list",
     *     options={"expose" = true}
     * )
     * @Method("GET")
     */
    public function getEntretiensAction(User $user)
    {
        if(!$user) {
            throw new \Exception('erreur object non trouvé', 500);
        }

        $entetiens = $this->getDoctrine()->getRepository('AppBundle:Entretien')
            ->findBy([
                'interviewee' => $user
            ])
        ;

        $models = [];
        foreach ($entetiens as $entetien) {
            $models[] = $this->createEntretienApiModel($entetien);
        }

        return $this->createApiResponse([
            'items' => $models
        ]);
    }



    /**
     * Finds and displays a entretien entity.
     *
     * @Route("/{id}/show",
     *     name="entretien_show",
     *     options={"expose" = true})
     * @Method("GET")
     */
    public function showAction(Entretien $entretien)
    {
        dump($entretien);
        if(!$entretien) {
            throw new \Exception('erreur object non trouvé', 500);
        }

        $entetien = $this->getDoctrine()->getRepository('AppBundle:Entretien')
            ->find($entretien->getId())
        ;

        $model = $this->createEntretienApiModel($entetien);

        return $this->createApiResponse([
            'item' => $model
        ]);
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

    private function createEntretienApiModel(Entretien $entretien)
    {

        $model = new EntretienApiModel();
        $model->id = $entretien->getId();
        $model->compteRendu = $entretien->getCompteRendu();
        $model->objet = $entretien->getObjet();
        $model->date = $entretien->getDate();

        $selfUrl = $this->generateUrl(
            'entretien_list',
            ['id' => $entretien->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }
}
