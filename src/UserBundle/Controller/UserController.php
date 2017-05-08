<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Form\RegisterEditType;
use UserBundle\Form\RegisterType;

/**
 * User controller.
 *
 * @Route("user")
 */

class UserController extends Controller
{
    /**
     * @Route("/", name="user_index")
     * @Security("has_role('ROLE_STAFF') or has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository('UserBundle:User')->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);
        if($form->isValid())
        {
            /** @var User $user */
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', [ 'id' => $user->getId()]);

        }
        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/{id}/show",
     *     name="user_show",
     *     options = { "expose" = true }
     *     )
     */
    public function showAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('DocumentationBundle\Form\DocumentType');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $document = $form->getData();
            $document->setAuthor($this->getUser());
            $document->setDestinataire($this->getUser());

            $em->persist($document);
            $em->flush($document);

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        $user = $em->getRepository('UserBundle:User')
            ->find($user);

        $documents = $this->getDoctrine()->getRepository('DocumentationBundle:Document')->findAll();
        return $this->render('user/show.html.twig', [
            'documents' => $documents,
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     *
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        $form = $this->createForm(RegisterEditType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var User $user */
            $user = $form->getData();
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_show', [ 'id' => $user->getId()]);
        }
        return $this->render('user/edit.html.twig', [
            'edit_form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'user' => $user
        ]);
    }

    /**
     * Deletes a member entity.
     *
     *
     * @Route("/{id}/delete", name="member_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush($user);
        }

        return $this->redirectToRoute('dashboard');
    }

    /**
     * Creates a form to delete a member entity.
     *
     * @param User $user The member entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('member_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * @Route("/json", name="json_user",
     *     options={"expose" = true}
     *     )
     *
     */
    /*public function jsonData()
    {
        $coach = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        //If admin is connected, he can view all the users
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {
            $users = $em->getRepository('UserBundle:User')
                ->findYoungsterByCoach($coach)->getQuery()->execute();
        } else {
            $users = $em->getRepository('UserBundle:User')
                ->findAll();
        }

        $listUsers = [];
        $i = 0;

        foreach ($users as $user)
        {
            $listUsers[$i]['id'] = $user->getId();
            $listUsers[$i]['firstname'] = $user->getFirstname();
            $listUsers[$i]['name'] = $user->getName();
            $listUsers[$i]['email'] = $user->getEmail();
            $listUsers[$i]['role'] = $user->getRole();
            $i++;
        }

        return new JsonResponse(['data' => $listUsers]);
    }*/
}
