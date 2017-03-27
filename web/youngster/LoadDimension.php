<?php
// src/AppBundle/DataFixtures/ORM/LoadInitiative.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Dimension;

class LoadDimension extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
      
        $greenTech = new Dimension();
        $greenTech->setName('Green Tech');
        $greenTech->setLogo($this->getReference('green_tech'));
        $this->addReference('Green Tech', $greenTech);

        $art_et_culture = new Dimension();
        $art_et_culture->setName('Art & Culture');
        $art_et_culture->setLogo($this->getReference('art_et_culture'));
        $this->addReference('Art & Culture', $art_et_culture);

        $green_finance = new Dimension();
        $green_finance->setName('Green Finance');
        $green_finance->setLogo($this->getReference('green_finance'));
        $this->addReference('Green Finance', $green_finance);

        $sante_bien_etre = new Dimension();
        $sante_bien_etre->setName('Santé & Bien-être');
        $sante_bien_etre->setLogo($this->getReference('sante_bien_etre'));
        $this->addReference('Santé & Bien-être', $sante_bien_etre);

        $agriculture_durable  = new Dimension();
        $agriculture_durable->setName('Agriculture Durable');
        $agriculture_durable->setLogo($this->getReference('agriculture_durable'));
        $this->addReference('Agriculture Durable', $agriculture_durable);

        $do_it_yourself  = new Dimension();
        $do_it_yourself->setName('Do it yourself');
        $do_it_yourself->setLogo($this->getReference('do_it_yourself'));
        $this->addReference('Do it yourself', $do_it_yourself);

        $mobilite_et_transport  = new Dimension();
        $mobilite_et_transport->setName('Mobilité & transports');
        $mobilite_et_transport->setLogo($this->getReference('mobilite_et_transport'));
        $this->addReference('Mobilité & transports', $mobilite_et_transport);

        $sensibilisation_formation  = new Dimension();
        $sensibilisation_formation->setName('Sensibilisation & Formation');
        $sensibilisation_formation->setLogo($this->getReference('sensibilisation_formation'));
        $this->addReference('Sensibilisation & Formation', $sensibilisation_formation);

        $alimentation_responsable  = new Dimension();
        $alimentation_responsable->setName('Alimentation Responsable');
        $alimentation_responsable->setLogo($this->getReference('alimentation_responsable'));
        $this->addReference('Alimentation Responsable', $alimentation_responsable);

        $enfants_jeunes  = new Dimension();
        $enfants_jeunes->setName('Enfants & jeunes');
        $enfants_jeunes->setLogo($this->getReference('enfants_jeunes'));
        $this->addReference('Enfants & jeunes', $enfants_jeunes);

        $mode_durable  = new Dimension();
        $mode_durable->setName('Mode durable');
        $mode_durable->setLogo($this->getReference('mode_durable'));
        $this->addReference('Mode durable', $mode_durable);

        $solidarite_et_entraide  = new Dimension();
        $solidarite_et_entraide->setName('Solidarité & Entraide');
        $solidarite_et_entraide->setLogo($this->getReference('solidarite_et_entraide'));
        $this->addReference('Solidarité & Entraide', $solidarite_et_entraide);

        $architecture_urbanisme_durable  = new Dimension();
        $architecture_urbanisme_durable->setName('Architecture & Urbanisme durable');
        $architecture_urbanisme_durable->setLogo($this->getReference('architecture_urbanisme_durable'));
        $this->addReference('Architecture & Urbanisme durable', $architecture_urbanisme_durable);

        $gestion_durable_de_lhabitat  = new Dimension();
        $gestion_durable_de_lhabitat->setName('Gestion durable de l\' habitat');
        $gestion_durable_de_lhabitat->setLogo($this->getReference('gestion_durable_de_lhabitat'));
        $this->addReference('Gestion durable de l\' habitat', $gestion_durable_de_lhabitat);

        $nature_biodiversite  = new Dimension();
        $nature_biodiversite->setName('Nature & Biodiversité ');
        $nature_biodiversite->setLogo($this->getReference('nature_biodiversite'));
        $this->addReference('Nature & Biodiversité', $nature_biodiversite);

        $tourisme_durable = new Dimension();
        $tourisme_durable->setName('Tourisme & loisirs');
        $tourisme_durable->setLogo($this->getReference('tourisme_durable'));
        $this->addReference('Tourisme & loisirs', $tourisme_durable);

        $manager->persist($greenTech);
        $manager->persist($art_et_culture);
        $manager->persist($green_finance);
        $manager->persist($sante_bien_etre);
        $manager->persist($agriculture_durable);
        $manager->persist($do_it_yourself);
        $manager->persist($mobilite_et_transport);
        $manager->persist($sensibilisation_formation);
        $manager->persist($alimentation_responsable);
        $manager->persist($enfants_jeunes);
        $manager->persist($mode_durable);
        $manager->persist($solidarite_et_entraide);
        $manager->persist($architecture_urbanisme_durable);
        $manager->persist($gestion_durable_de_lhabitat);
        $manager->persist($nature_biodiversite);
        $manager->persist($tourisme_durable);

        $manager->flush();
    }
    public function getOrder()
    {
        return 4;
    }
}