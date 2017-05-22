<?php

namespace AppBundle\Api;

class UserApiModel
{
    public $id;

    public $name;

    public $firstname;

    public $fullname;

    public $email;

    public $role;

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
