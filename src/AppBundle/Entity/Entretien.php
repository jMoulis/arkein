<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entretien
 *
 * @ORM\Table(name="entretien")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EntretienRepository")
 */
class Entretien
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
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="compteRendu", type="text")
     */
    private $compteRendu;

    /**
     * @var string
     *
     * @ORM\Column(name="objet", type="string", length=255)
     */
    private $objet;


    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $interviewer;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $interviewee;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

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
     * @return Entretien
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
     * @return Entretien
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
     * Set objet
     *
     * @param string $objet
     *
     * @return Entretien
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }


    /**
     * Set interviewer
     *
     * @param \UserBundle\Entity\User $interviewer
     *
     * @return Entretien
     */
    public function setInterviewer(\UserBundle\Entity\User $interviewer)
    {
        $this->interviewer = $interviewer;

        return $this;
    }

    /**
     * Get interviewer
     *
     * @return \UserBundle\Entity\User
     */
    public function getInterviewer()
    {
        return $this->interviewer;
    }

    /**
     * Set interviewee
     *
     * @param \UserBundle\Entity\User $interviewee
     *
     * @return Entretien
     */
    public function setInterviewee(\UserBundle\Entity\User $interviewee)
    {
        $this->interviewee = $interviewee;

        return $this;
    }

    /**
     * Get interviewee
     *
     * @return \UserBundle\Entity\User
     */
    public function getInterviewee()
    {
        return $this->interviewee;
    }
}