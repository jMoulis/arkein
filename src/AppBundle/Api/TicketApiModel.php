<?php

namespace AppBundle\Api;

class TicketApiModel
{
    public $id;

    public $date;

    public $auteur = [];

    public $message;

    public $niveau;

    public $reponses;

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