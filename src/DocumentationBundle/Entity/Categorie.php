<?php

namespace DocumentationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Categorie
 *
 *
 * @ORM\Table(name="categorie")
 * @ORM\Entity(repositoryClass="DocumentationBundle\Repository\CategorieRepository")
 */
class Categorie
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="Veuillez définir un nom de dossier")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="DocumentationBundle\Entity\Document", mappedBy="categorie")
     */
    private $documents;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $owner;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPrivate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $classified;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->isPrivate = true;
        $this->classified = false;
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
     * Set name
     *
     * @param string $name
     *
     * @return Categorie
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add document
     *
     * @param \DocumentationBundle\Entity\Document $document
     *
     * @return Categorie
     */
    public function addDocument($document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param \DocumentationBundle\Entity\Document $document
     */
    public function removeDocument($document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }


    public function __toString()
    {
        return $this->getName();
    }


    /**
     * @return mixed
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * @param mixed $isPrivate
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;
    }


    /**
     * Set owner
     *
     * @param \UserBundle\Entity\User $owner
     *
     * @return Categorie
     */
    public function setOwner(\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return mixed
     */
    public function getClassified()
    {
        return $this->classified;
    }

    /**
     * @param mixed $classified
     */
    public function setClassified($classified)
    {
        $this->classified = $classified;
    }


}
