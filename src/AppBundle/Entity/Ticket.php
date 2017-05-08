<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TicketRepository")
 */
class Ticket
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
     * @ORM\Column(type="string")
     */
    private $objet;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="smallint")
     */
    private $statut;

    /**
     * @ORM\Column(type="string")
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $fromWho;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $toWho;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $aboutWho;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Answer", mappedBy="ticket", cascade={"persist"})
     * @ORM\OrderBy({"dateCreated" = "DESC"})
     */
    private $answers;

    public function __construct()
    {
        $this->statut = 1;
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
     * @return mixed
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * @param mixed $objet
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Ticket
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Ticket
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
     * Set statut
     *
     *
     *
     * @return Ticket
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     *
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set fromWho
     *
     * @param User $fromWho
     *
     * @return Ticket
     */
    public function setFromWho(User $fromWho = null)
    {
        $this->fromWho = $fromWho;

        return $this;
    }

    /**
     * Get fromWho
     *
     * @return User
     */
    public function getFromWho()
    {
        return $this->fromWho;
    }

    /**
     * Set toWho
     *
     * @param User $toWho
     *
     * @return Ticket
     */
    public function setToWho(User $toWho = null)
    {
        $this->toWho = $toWho;

        return $this;
    }

    /**
     * Get toWho
     *
     * @return User
     */
    public function getToWho()
    {
        return $this->toWho;
    }

    /**
     * Set aboutWho
     *
     * @param User $aboutWho
     *
     * @return Ticket
     */
    public function setAboutWho(User $aboutWho = null)
    {
        $this->aboutWho = $aboutWho;

        return $this;
    }

    /**
     * Get aboutWho
     *
     * @return User
     */
    public function getAboutWho()
    {
        return $this->aboutWho;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Add answer
     *
     * @param Answer $answer
     *
     * @return Ticket
     */
    public function addAnswer(Answer $answer)
    {
        $this->answers[] = $answer;
        $answer->setTicket($this);

        return $this;
    }

    /**
     * Remove answer
     *
     * @param Answer $answer
     */
    public function removeAnswer(Answer $answer)
    {
        $this->answers->removeElement($answer);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}
