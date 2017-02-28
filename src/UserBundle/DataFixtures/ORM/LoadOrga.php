<?php
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\Organization;

class LoadOrga extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function load(ObjectManager $manager)
    {
        $organization1 = new Organization();
        $organization1->setName('Centuriae canities');
        $organization1->setDescription('His cognitis Gallus ut serpens adpetitus telo vel saxo iamque spes extremas opperiens et succurrens saluti suae quavis ratione colligi omnes iussit armatos et cum starent attoniti, districta dentium acie stridens adeste inquit viri fortes mihi periclitanti vobiscum.');
        $organization1->setDateCreated(new \DateTime());
        $organization1->setCredential($this->getReference('credOrga1'));
        $this->addReference('organization1', $organization1);

        $organization2 = new Organization();
        $organization2->setName('Neque porro quisquam');
        $organization2->setDescription('Cum haec taliaque sollicitas eius aures everberarent expositas semper eius modi rumoribus et patentes, varia animo tum miscente consilia, tandem id ut optimum factu elegit: et Vrsicinum primum ad se venire summo cum honore mandavit ea specie ut pro rerum tunc urgentium captu disponeretur concordi consilio, quibus virium incrementis Parthicarum gentium a arma minantium impetus frangerentur.');
        $organization2->setDateCreated(new \DateTime());
        $organization2->setCredential($this->getReference('credOrga2'));
        $this->addReference('organization2', $organization2);

        $manager->persist($organization1);
        $manager->persist($organization2);
        $manager->flush();
    }
    public function getOrder()
    {
        return 2;
    }
}