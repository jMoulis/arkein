<?php

namespace DocumentationBundle\Controller;

use AppBundle\Api\CategoryApiModel;
use AppBundle\Controller\BaseController;
use DocumentationBundle\Entity\Categorie;
use DocumentationBundle\Form\Type\CategorieType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use UserBundle\Entity\User;

/**
 * @Route("categorie")
 */

class ApiCategoryController extends BaseController
{

    /**
     * Lists all document entities.
     *
     * @Route("/", name="categorie_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('DocumentationBundle:Categorie')->findBy([
            'owner' => null,
            'classified' => 0
        ]);

        return $this->render('document/categorie/index.html.twig', array(
            'categories' => $categories
        ));
    }

    /**
     * @Route("/api/new/cat/{userid}",
     *     name="api_category_new",
     *     options={"expose" = true}
     * )
     * @Method("POST")
     */
    public function newCategoryAction(Request $request, $userid = null)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(CategorieType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        /** @var Categorie $category */
        $category = $form->getData();
        $em = $this->getDoctrine()->getManager();
        if($userid){
            $category->setOwner($em->getRepository('UserBundle:User')->find($userid));
        }

        $em->persist($category);
        $em->flush();

        $apiModel = $this->createCategoryApiModel($category);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
            'Location',
            $this->generateUrl('cat_get', ['id' => $category->getId()])
        );

        return $response;
    }

    /**
     * @Route("/api/list/{userid}/cat",
     *     name="category_list",
     *     options={"expose" = true}
     *     )
     * @Method("GET")
     */
    public function getCategoriesByUserAction($userid)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($userid);

        $categories = $this->getDoctrine()->getRepository('DocumentationBundle:Categorie')
            ->findOrderASCCategorie($user);
        $models = [];
        foreach ($categories as $category) {
            $models[] = $this->createCategoryApiModel($category);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * Used to load the categories for the treeview
     * @Route("/api/list/categories/{id}",
     *     name="categorie_treeview",
     *     options={"expose" = true})
     */
    public function loadCategoriesAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($id);
        $categories = $em->getRepository('DocumentationBundle:Categorie')->findOrderASCCategorie($user);

        $models = [];
        foreach ($categories as $category) {
            $models[] = $this->createCategoryApiModel($category);
        }

        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("/api/list/{id}/cat/", name="cat_get")
     * @Method("GET")
     */
    public function getCategoryByIdAction(Categorie $category)
    {
        $apiModel = $this->createCategoryApiModel($category);

        return $this->createApiResponse($apiModel);
    }

    /**
     * @Route("/api/modal/cat/{id}/show",
     *     name="categorie_modal_detail",
     *     options={"expose" = true})
     * @Method("GET")
     */
    public function detailModalAction(Categorie $categorie)
    {

        if(!$categorie) {
            throw new \Exception('erreur object non trouvé', 500);
        }
        $categorie = $this->getDoctrine()->getRepository('DocumentationBundle:Categorie')
            ->find($categorie->getId())
        ;
        $model = $this->createCategoryApiModel($categorie);
        return $this->createApiResponse([
            'item' => $model
        ]);
    }

    /**
     * @Route("/api/edit/{catid}/cat/{userid}",
     *     name="api_category_edit",
     *     options={"expose" = true}
     * )
     * @Method("POST")
     */
    public function editCategoryAction(Request $request, $catid ,$userid = null)
    {

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(CategorieType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        /** @var Categorie $category */

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('DocumentationBundle:Categorie')->find($catid);

        if($userid){
            $category->setOwner($em->getRepository('UserBundle:User')->find($userid));
        }
        $category->setName($data['name']);

        if($data['classified'] === true)
        {

        }
        $category->setClassified($data['classified']);

        $em->persist($category);
        $em->flush();

        $apiModel = $this->createCategoryApiModel($category);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
            'Location',
            $this->generateUrl('cat_get', ['id' => $category->getId()])
        );

        return $response;
    }

    /**
     * @Route("/api/delete/{id}/cat/", name="cat_delete")
     * @Method("DELETE")
     */
    public function deleteRepLogAction(Categorie $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return new Response(null, 204);
    }

    /**
     * This could be moved into a service if it needed to be
     * re-used elsewhere.
     *
     * @param Categorie $category
     * @return CategoryApiModel
     */
    private function createCategoryApiModel(Categorie $category)
    {
        $model = new CategoryApiModel();
        $model->id = $category->getId();
        $model->name = $category->getName();
        $model->isPrivate = $category->getIsPrivate();
        $model->documentCount = count($category->getDocuments());
        $model->archive = $category->getClassified();

        if($category->getOwner())
        {
            $model->owner = $category->getOwner()->getFullName();
        }

        foreach ($category->getDocuments() as $document) {
            $model->documents[] = [
                'file' => $document->getFileTemporary(),
                'name' => $document->getFileName(),
                'id' => $document->getId()
            ];
        }

        $selfUrl = $this->generateUrl(
            'cat_get',
            ['id' => $category->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }
}
