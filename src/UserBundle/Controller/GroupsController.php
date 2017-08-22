<?php

namespace UserBundle\Controller;

use AppBundle\Api\GroupsApiModel;
use AppBundle\Controller\BaseController;
use UserBundle\Entity\Groups;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("groups")
 */

class GroupsController extends BaseController
{
    /**
     * @Route("/api/groups", name="api_ticket_themes_list", options={"expose" = true})
     * @Method("GET")
     */
    public function getGroupsAction()
    {
        $groups = $this->getDoctrine()->getRepository(Groups::class)->findAll();
        $models = [];
        foreach ($groups as $group) {
            $models[] = $this->createGroupsApiModel($group);
        }
        return $this->createApiResponseAction([
            'items' => $models
        ]);
    }

    /**
     * @param Groups $group
     * @return GroupsApiModel
     */
    private function createGroupsApiModel(Groups $group)
    {
        $model = new GroupsApiModel();
        $model->id = $group->getId();
        $model->name = $group->getName();

        $selfUrl = $this->generateUrl(
            'user_show',
            ['id' => $group->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }

}
