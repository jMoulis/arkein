<?php

namespace AppBundle\Api;

class RepLogApiModel
{
    public $id;

    public $fileTemporary;

    public $fileName;


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