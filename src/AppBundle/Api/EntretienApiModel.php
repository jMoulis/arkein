<?php

namespace AppBundle\Api;

class EntretienApiModel
{
    public $id;

    public $compteRendu;

    public $compteRenduLien;

    public $objet;

    public $odj;

    public $author;

    public $authorId;

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