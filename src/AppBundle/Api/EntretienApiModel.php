<?php

namespace AppBundle\Api;

class EntretienApiModel
{
    public $id;

    public $compteRendu;

    public $objet;

    public $author;

    public $young;

    public $youngId;

    public $guests = [];

    public $date;

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