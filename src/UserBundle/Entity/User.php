<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 11/03/2017
 * Time: 13:14
 */

namespace UserBundle\Entity;

use AppBundle\Entity\Address;
use AppBundle\Entity\Answer;
use AppBundle\Entity\Phone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DocumentationBundle\Entity\Document;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="Cet email est déjà inscrit!")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $firstname;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     *
     * @var string
     */
    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Phone",
     *     mappedBy="user",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     *     )
     */
    private $phoneNumbers;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Address",
     *     mappedBy="user",
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     *     )
     */
    private $addresses;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Answer", mappedBy="user")
     */
    private $answers;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", inversedBy="youngsters")
     * @ORM\JoinTable(name="youngster_coach")
     */
    private $coach;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", mappedBy="coach")
     */
    private $youngsters;
    /**
     * @ORM\OneToMany(targetEntity="DocumentationBundle\Entity\Document", mappedBy="destinataire")
     */
    private $documents;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\Groups", inversedBy="users")
     */
    private $groups;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\InterviewUser", mappedBy="user")
     */
    private $guestInterviews;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $titre;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->phoneNumbers = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->coach = new ArrayCollection();
        $this->youngsters = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->guestInterviews = new ArrayCollection();
        $this->isActive = true;
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string")
     */
    private $role;

    public function getId()
    {
        return $this->id;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        $this->password = null;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function __toString()
    {
        return (string) $this->firstname .', '. $this->name.' - '. $this->titre;
    }

    public function getFullName()
    {
        return (string) $this->firstname .', '. $this->name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function addPhoneNumber(Phone $phoneNumber)
    {

        $this->phoneNumbers[] = $phoneNumber;
        $phoneNumber->setUser($this);
    }

    public function removePhoneNumber(Phone $phoneNumber)
    {
        $this->phoneNumbers->removeElement($phoneNumber);
        $phoneNumber->setUser(null);
    }

    /**
     * @return ArrayCollection|Phone[]
     */
    public function getPhoneNumbers()
    {
        return $this->phoneNumbers;
    }


    public function addAddress(Address $address)
    {
        $this->addresses[] = $address;
        $address->setUser($this);

        return $this;
    }

    public function removeAddress(Address $address)
    {
        $this->addresses->removeElement($address);
        $address->setUser(null);
    }

    /**
     * Get addresses
     *
     * @return ArrayCollection|Address[]
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    public function getRoles()
    {
        $roles = [$this->role];

        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        return $roles;
    }

    public function setRoles(array $roles)
    {
        throw new \Exception("Erreur sur l'enregistrement des roles");
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * @param mixed $titre
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }


    /**
     * Add answer
     *
     * @param Answer $answer
     *
     * @return User
     */
    public function addAnswer(Answer $answer)
    {
        $this->answers[] = $answer;
        $answer->setUser($this);

        return $this;
    }

    /**
     * Remove answer
     *
     * @param Answer $answer
     */
    public function removeAnswer(Answer $answer)
    {
        $this->answer->removeElement($answer);
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

    /**
     * @return mixed
     */
    public function getCoach()
    {
        return $this->coach;
    }

    /**
     * Add coach
     *
     * @param User $coach
     *
     * @return User
     */
    public function addCoach(User $coach)
    {
        $this->coach[] = $coach;

        return $this;
    }

    /**
     * Remove coach
     *
     * @param User $coach
     */
    public function removeCoach(User $coach)
    {
        $this->coach->removeElement($coach);
    }

    /**
     * Add document
     *
     * @param \DocumentationBundle\Entity\Document $document
     *
     * @return User
     */
    public function addDocument(Document $document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param \DocumentationBundle\Entity\Document $document
     */
    public function removeDocument(Document $document)
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

    /**
     * Add group
     *
     * @param Groups $group
     *
     * @return User
     */
    public function addGroup(Groups $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param Groups $group
     */
    public function removeGroup(Groups $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add youngster
     *
     * @param User $youngster
     *
     * @return User
     */
    public function addYoungster(User $youngster)
    {
        $this->youngsters[] = $youngster;

        return $this;
    }

    /**
     * Remove youngster
     *
     * @param User $youngster
     */
    public function removeYoungster(User $youngster)
    {
        $this->youngsters->removeElement($youngster);
    }

    /**
     * Get youngsters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getYoungsters()
    {
        return $this->youngsters;
    }

    /**
     * Get guestInterviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGuestInterviews()
    {
        return $this->guestInterviews;
    }
}
