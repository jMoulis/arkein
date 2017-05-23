<?php

namespace UserBundle\Controller;

use AppBundle\Api\UserApiModel;
use AppBundle\Controller\BaseController;
use DocumentationBundle\Form\Type\DocumentType;
use UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use UserBundle\Form\Type\RegisterEditType;
use UserBundle\Form\Type\RegisterType;

/**
 * @Route("user")
 */

class UserController extends BaseController
{
    /**
     * @Route("/", name="user_index")
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository('UserBundle:User')->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/api/youngs/", name="young_list", options={"expose" = true})
     * @Method("GET")
     */
    public function getYoungAction()
    {
        if($this->getUser()->getRole() === 'ROLE_ADMIN')
        {
            $users = $this->getDoctrine()->getRepository('UserBundle:User')
                ->findBy([
                    'isActive' => 1
                ]);
        } else {
            $users = $this->getDoctrine()->getRepository('UserBundle:User')
                ->findMyYoungsters($this->getUser())->getQuery()->execute();

        }
        $models = [];
        foreach ($users as $user) {
            $models[] = $this->createUserApiModel($user);
        }
        return $this->createApiResponse([
            'items' => $models
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
     *
     * @Route("/api/{user}/guests/", name="guest_list", options={"expose" = true})
     * @Method("GET")
     */
    public function getGuestsAction($user)
    {
        $users = $this->getDoctrine()->getRepository('UserBundle:User')
            ->findYoungAllCoaches($user);
        $models = [];
        foreach ($users as $user) {
            $models[] = $this->createUserApiModel($user);
        }
        return $this->createApiResponse([
            'items' => $models
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
        $form = $this->createForm(DocumentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $document = $form->getData();
            $document->setAuthor($this->getUser());
            $document->setDestinataire($this->getUser());

            $em->persist($document);
            $em->flush();

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
     *
     * @Route("/api/users/", name="user_list", options={"expose" = true})
     * @Method("GET")
     */
    public function getAllUsersAction()
    {
        $users = $this->getDoctrine()->getRepository('UserBundle:User')
            ->findAll();
        $models = [];
        foreach ($users as $user) {
            $models[] = $this->createUserApiModel($user);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("api/user/{id}/edit", name="api_user_edit", options={"expose" = true})
     * @Method("POST")
     */
    public function editUserAction(Request $request)
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
                $user = $em->getRepository('UserationBundle:User')->findOneBy(['id' => $params['id']]);
                $em->persist($user);
                $em->flush();
            }
            return new JsonResponse(['data' => $params]);
        }
        return new Response("Error", 400);
    }

    /**
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, User $user)
    {
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
            'user' => $user
        ]);
    }

    /**
     * @Route("/api/user/{id}", name="api_user_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteUserAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * @param User $user
     * @return UserApiModel
     */
    private function createUserApiModel(User $user)
    {
        $model = new UserApiModel();
        $model->id = $user->getId();
        $model->name = $user->getName();
        $model->firstname = $user->getFirstname();
        $model->fullname = $user->getFullName();
        $model->email = $user->getEmail();
        $model->role = $user->getRole();

        $selfUrl = $this->generateUrl(
            'user_show',
            ['id' => $user->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }

}
