<?php

namespace AppBundle\Api;

class FileSystemApiModel
{
    public $folderId;

    public $folderName;

    public $files;

    public $folderPath;

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
