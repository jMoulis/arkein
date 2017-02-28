<?php
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\Credential;

class LoadCredential extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    private function encodePassword(Credential $credential, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($credential);
        return $encoder->encodePassword($plainPassword, $credential->getSalt());
    }
    public function load(ObjectManager $manager)
    {
        $credOrga1 = new Credential();
        $credOrga1->setUsername('orga1');
        $credOrga1->setUsernameCanonical('orga1');
        $credOrga1->setEmail('julien.moulis@me.com');
        $credOrga1->setEmailCanonical('julien.moulis@me.com');
        $credOrga1->setEnabled(1);
        $credOrga1->setPassword($this->encodePassword($credOrga1, 'test'));
        $credOrga1->setRoles(array('ROLE_ORGANIZATION'));
        $this->addReference('credOrga1', $credOrga1);

        $credOrga2 = new Credential();
        $credOrga2->setUsername('orga2');
        $credOrga2->setUsernameCanonical('orga2');
        $credOrga2->setEmail('julien.moulis@mac.com');
        $credOrga2->setEmailCanonical('julien.moulis@mac.com');
        $credOrga2->setEnabled(1);
        $credOrga2->setPassword($this->encodePassword($credOrga2, 'test'));
        $credOrga2->setRoles(array('ROLE_ORGA'));
        $this->addReference('credOrga2', $credOrga2);

        $credAdmin = new Credential();
        $credAdmin->setUsername('admin');
        $credAdmin->setEmail('julien.moulis@moulis.me');
        $credAdmin->setEmailCanonical('julien.moulis@moulis.me');
        $credAdmin->setEnabled(1);
        $credAdmin->setPassword($this->encodePassword($credAdmin, 'test'));
        $credAdmin->setRoles(array('ROLE_ADMIN'));

        $credMember1 = new Credential();
        $credMember1->setUsername('customer1');
        $credMember1->setEmail('julien.moulis21@gmail.com');
        $credMember1->setEmailCanonical('julien.moulis21@gmail.com');
        $credMember1->setEnabled(1);
        $credMember1->setPassword($this->encodePassword($credMember1, 'test'));
        $credMember1->setRoles(array('ROLE_MEMBER'));
        $this->addReference('credMember1', $credMember1);

        $credMember2 = new Credential();
        $credMember2->setUsername('customer2');
        $credMember2->setEmail('julien@gmail.com');
        $credMember2->setEmailCanonical('julien@gmail.com');
        $credMember2->setEnabled(1);
        $credMember2->setPassword($this->encodePassword($credMember2, 'test'));
        $credMember2->setRoles(array('ROLE_MEMBER'));
        $this->addReference('credMember2', $credMember2);
        

        $manager->persist($credOrga1);
        $manager->persist($credOrga2);
        $manager->persist($credAdmin);
        $manager->persist($credMember1);
        $manager->persist($credMember2);
        $manager->flush();
    }
    public function getOrder()
    {
        return 1;
    }
}