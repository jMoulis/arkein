<?php
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\Member;

class LoadMember extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function load(ObjectManager $manager)
    {
        $member1 = new Member();
        $member1->setName('Doe');
        $member1->setFirstname('John');
        $member1->setCredential($this->getReference('credMember1'));

        $member2 = new Member();
        $member2->setName('Doe');
        $member2->setFirstname('Jane');
        $member2->setCredential($this->getReference('credMember2'));

        $manager->persist($member1);
        $manager->persist($member2);
        $manager->flush();
    }
    public function getOrder()
    {
        return 2;
    }
}