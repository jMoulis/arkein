<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 27/02/2017
 * Time: 07:31
 */

namespace UserBundle\Twig;



use Symfony\Bridge\Doctrine\RegistryInterface;

class FindOrga extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('findOrga', array($this, 'findOrga')),
        );
    }

    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function findOrga($id){
        $em = $this->doctrine->getManager();
        $orga = $em->getRepository('UserBundle:Organization')->findOneByCredential($id);

        return $orga->getId();
    }

    public function getName()
    {
        return 'twig_find_Orga';
    }

}