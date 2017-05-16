<?php

namespace BlogBundle\Controller;

use AppBundle\Api\BilletApiModel;
use AppBundle\Controller\BaseController;
use BlogBundle\Entity\Billet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("blog")
 *
 */
class BlogController extends BaseController
{

    /**
     * @Route("/", name="index_blog")
     */
    public function indexAction()
    {
        return $this->render(':blog:index.html.twig');
    }

    /**
     * @Route("/api/billets/list", name="api_billet_list", options={"expose" = true})
     * @Method("GET")
     */
    public function getBilletsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $billets = $em->getRepository('BlogBundle:Billet')->findAll();
        $models = [];
        foreach ($billets as $billet) {
            $models[] = $this->createBilletApiModel($billet);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @param Billet $billet
     * @return BilletApiModel
     */
    private function createBilletApiModel(Billet $billet)
    {
        $model = new BilletApiModel();
        $model->id = $billet->getId();
        $model->date = $billet->getDate()->format('d/M/y');
        $model->contenu = $billet->getContenu();
        $model->auteur = $billet->getAuthor()->getFullName();
        $model->titre = $billet->getTitre();

        $selfUrl = $this->generateUrl(
            'billet_show',
            ['id' => $billet->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }
}
