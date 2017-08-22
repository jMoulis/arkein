<?php

namespace AppBundle\Api;

class TicketApiModel
{
    public $id;

    public $date;

    public $auteur = [];

    public $auteurId;

    public $titre;

    public $message;

    public $niveau;

    public $reponses;

    public $auteurEmail;

    public $destinataire;

    public $statut;

    public $themes;

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
