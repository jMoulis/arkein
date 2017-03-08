<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 27/02/2017
 * Time: 07:31
 */

namespace UserBundle\Twig;



use Symfony\Bridge\Doctrine\RegistryInterface;

class FindMember extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('findMember', array($this, 'findMember')),
        );
    }

    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function findMember($id){
        $em = $this->doctrine->getManager();
        $member = $em->getRepository('UserBundle:Member')->findOneByCredential($id);

        return $member->getId();
    }

    public function getName()
    {
        return 'twig_find_Member';
    }

}