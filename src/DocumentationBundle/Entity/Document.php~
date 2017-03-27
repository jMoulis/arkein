<?php

namespace DocumentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="DocumentationBundle\Repository\DocumentRepository")
 * @Vich\Uploadable()
 *
 */
class Document
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
     * @ORM\Column(name="fileName", type="string", length=255, nullable=true)
     *
     */
    private $fileName;

    /**
     * @Vich\UploadableField(mapping="file_youngster", fileNameProperty="fileName")
     * @Assert\NotBlank(message="Veuillez sélectionner un fichier")
     *
     * @var File
     */
    private $fileTemporary;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
    */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="documents")
     */
    private $destinataire;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentationBundle\Entity\Categorie", inversedBy="documents")
     * @Assert\NotBlank(message="Veuillez sélectionner le dossier")
     */
    private $categorie;


    /**
     * Contructeur
     */
    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return Document
     */
    public function setFileTemporary(File $file = null)
    {
        $this->fileTemporary = $file;

        if ($file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFileTemporary()
    {
        return $this->fileTemporary;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getDestinataire()
    {
        return $this->destinataire;
    }

    /**
     * @param mixed $destinataire
     */
    public function setDestinataire($destinataire)
    {
        $this->destinataire = $destinataire;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Document
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }



    /**
     * Set categorie
     *
     * @param \DocumentationBundle\Entity\Categorie $categorie
     *
     * @return Document
     */
    public function setCategorie(\DocumentationBundle\Entity\Categorie $categorie = null)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \DocumentationBundle\Entity\Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    public function __toString()
    {
        return $this->getFileName();
    }



}
