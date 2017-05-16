<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CompteRendu
 *
 * @ORM\Table(name="compte_rendu")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompteRenduRepository")
 */
class CompteRendu
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\NotBlank(message="Merci de remplir la date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="compteRendu", type="text")
     * @Assert\NotBlank(message="Merci de remplir le compte rendu")
     */
    private $compteRendu;

    /**
     * @var string
     *
     * @ORM\Column(name="presence", type="text", nullable=true)
     */
    private $presence;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Entretien", inversedBy="compteRendu")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Une erreur est survenue, veuillez recharcher la page")
     */
    private $entretien;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lienpdf;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return CompteRendu
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set compteRendu
     *
     * @param string $compteRendu
     *
     * @return CompteRendu
     */
    public function setCompteRendu($compteRendu)
    {
        $this->compteRendu = $compteRendu;

        return $this;
    }

    /**
     * Get compteRendu
     *
     * @return string
     */
    public function getCompteRendu()
    {
        return $this->compteRendu;
    }

    /**
     * Set presence
     *
     * @param string $presence
     *
     * @return CompteRendu
     */
    public function setPresence($presence)
    {
        $this->presence = $presence;

        return $this;
    }

    /**
     * Get presence
     *
     * @return string
     */
    public function getPresence()
    {
        return $this->presence;
    }

    /**
     * Set entretien
     *
     * @param Entretien $entretien
     *
     * @return CompteRendu
     */
    public function setEntretien(Entretien $entretien)
    {
        $this->entretien = $entretien;

        return $this;
    }

    /**
     * Get entretien
     *
     * @return Entretien
     */
    public function getEntretien()
    {
        return $this->entretien;
    }

    /**
     * @return mixed
     */
    public function getLienpdf()
    {
        return $this->lienpdf;
    }

    /**
     * @param mixed $lienpdf
     */
    public function setLienpdf($lienpdf)
    {
        $this->lienpdf = $lienpdf;
    }


}
