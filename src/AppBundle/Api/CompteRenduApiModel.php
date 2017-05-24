<?php

namespace AppBundle\Api;

class CompteRenduApiModel
{
    public $id;

    public $compteRendu;

    public $date;

    public $presence;

    public $entretien;

    public $authorId;

    public $lienPdf;

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
