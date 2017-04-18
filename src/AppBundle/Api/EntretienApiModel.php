<?php

namespace AppBundle\Api;

class EntretienApiModel
{
    public $id;

    public $compteRendu;

    public $objet;

    public $interviewer;

    public $interviewee;

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