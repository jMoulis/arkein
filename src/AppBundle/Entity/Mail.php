<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mail
 *
 * @ORM\Table(name="mail")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MailRepository")
 */
class Mail
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
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "L'email '{{ value }}' n'est pas valide.",
     *     checkMX=true
     * )
     *
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\NotBlank(message = "Merci de renseigner votre nom")
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Assert\NotBlank(message = "Merci de renseigner votre prénom")
     */
    private $prenom;

    /**
     * @var string
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank( message = "Merci de renseigner votre message")
     * @Assert\Length(
     *     min = 10,
     *     minMessage = "Votre message est trop court"
     * )
     *
     *
     */
    private $message;

    /**
     * @ORM\Column(type="string")
     */
    private $sujet;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;


    public function __construct()
    {
        $this->dateCreated = new \DateTime();
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
     * Set mail
     *
     * @param string $mail
     *
     * @return Mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Mail
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Mail
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Mail
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set sujet
     *
     * @param string $sujet
     *
     * @return Mail
     */
    public function setSujet($sujet)
    {
        $this->sujet = $sujet;

        return $this;
    }

    /**
     * Get sujet
     *
     * @return string
     */
    public function getSujet()
    {
        return $this->sujet;
    }
}
