<?php

namespace UserBundle\Controller;

use AppBundle\Api\UserApiModel;
use AppBundle\Controller\BaseController;
use UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("user")
 */

class ApiUserController extends BaseController
{

    /**
     *
     * @Route("/api/youngs/", name="young_list", options={"expose" = true})
     * @Method("GET")
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository('UserBundle:User')
            ->findMyYoungsters($this->getUser())->getQuery()->execute();

        $models = [];
        foreach ($users as $user) {
            $models[] = $this->createUserApiModel($user);
        }
        return $this->createApiResponse([
            'items' => $models
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
     * @Route("api/user/edit", name="api_user_edit", options={"expose" = true})
     * @Method("POST")
     */
    public function editUser(Request $request)
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
     * @Route("/api/user/{id}", name="api_user_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteUserAction(User $user)
    {
        /*$this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');*/
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
        $model->fullname = $user->__toString();
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
