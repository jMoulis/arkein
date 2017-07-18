<?php

namespace AppBundle\Api;

class InterviewUserApiModel
{
    public $id;

    public $user;

    public $status;

    public $interview;

    private $links = [];

    public function addLink($ref, $url)
    {
        $this->links[$ref] = $url;
    }

    public function getLinks()
    {
        return $this->links;
    }
}
