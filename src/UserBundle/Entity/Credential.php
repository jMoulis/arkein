<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Credential
 *
 * @ORM\Table(name="credential")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\CredentialRepository")
 */
class Credential extends BaseUser
{
    /**
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\Member", mappedBy="credential")
     */
    private $member;

    /**
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\Organization", mappedBy="credential")
     */
    private $organization;

    protected $roles = array();
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
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
     * Set member
     *
     * @param \UserBundle\Entity\Member $member
     *
     * @return Credential
     */
    public function setMember(\UserBundle\Entity\Member $member = null)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \UserBundle\Entity\Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set organization
     *
     * @param \UserBundle\Entity\Organization $organization
     *
     * @return Credential
     */
    public function setOrganization(\UserBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \UserBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
