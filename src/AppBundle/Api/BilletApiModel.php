<?php

namespace AppBundle\Api;

class BilletApiModel
{
    public $id;

    public $date;

    public $titre;

    public $auteur;

    public $contenu;

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