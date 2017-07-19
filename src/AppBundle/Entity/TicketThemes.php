<?php
/**
 * Created by PhpStorm.
 * User: mint
 * Date: 19/07/17
 * Time: 16:36
 */

namespace AppBundle\Entity;


class TicketThemes
{
    private $themes = [
        [
            "value" => "scolaire",
            "label" => "Scolarité"
        ],
        [
            "value" => "incident",
            "label" => "Incident"
        ],
        [
            "value" => "personnel",
            "label" => "Personnel"
        ],
        [
            "value" => "medical",
            "label" => "Médical"
        ],
    ];

    /**
     * @return array
     */
    public function getThemes()
    {
        return $this->themes;
    }
}