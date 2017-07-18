<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * InterviewUser
 *
 * @ORM\Table(name="interview_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterviewUserRepository")
 */
class InterviewUser
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
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Entretien", inversedBy="interviewGuests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $interview;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="guestInterviews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    public function __construct()
    {
        $this->status = 0;
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
     * Set status
     *
     * @param integer $status
     *
     * @return InterviewUser
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getInterview()
    {
        return $this->interview;
    }

    /**
     * @param mixed $interview
     */
    public function setInterview($interview)
    {
        $this->interview = $interview;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
