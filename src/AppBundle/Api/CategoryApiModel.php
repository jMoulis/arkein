<?php

namespace AppBundle\Api;

class CategoryApiModel
{
    public $id;

    public $name;

    public $isPrivate;

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