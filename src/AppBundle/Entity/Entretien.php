<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="Merci de remplir le compte rendu")
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
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $young;

    /**
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\InterviewUser",
     *     mappedBy="interview",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"})
     */
    private $interviewGuests;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isArchived;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->interviewGuests = new ArrayCollection();
        $this->isArchived = false;
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
     * Set isArchived
     *
     * @param boolean $isArchived
     *
     * @return Entretien
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }


    /**
     * Set author
     *
     * @param User $author
     *
     * @return Entretien
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set young
     *
     * @param User $young
     *
     * @return Entretien
     */
    public function setYoung(User $young)
    {
        $this->young = $young;

        return $this;
    }

    /**
     * Get young
     *
     * @return User
     */
    public function getYoung()
    {
        return $this->young;
    }

    /**
     * Add interviewGuest
     *
     * @param InterviewUser $interviewGuest
     *
     * @return Entretien
     */
    public function addInterviewGuest(InterviewUser $interviewGuest)
    {
        $this->interviewGuests[] = $interviewGuest;

        $interviewGuest->setInterview($this);

        return $this;
    }

    /**
     * Remove interviewGuest
     *
     * @param InterviewUser $interviewGuest
     */
    public function removeInterviewGuest(InterviewUser $interviewGuest)
    {
        $this->interviewGuests->removeElement($interviewGuest);
    }

    /**
     * Get interviewGuests
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInterviewGuests()
    {
        return $this->interviewGuests;
    }

}
