<?php
// src/AppBundle/DataFixtures/ORM/LoadInitiative.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Initiative;

class LoadInitiative extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $name = array('initiative1',
                              'initiative2',
                              'initiative3',
                              'initiative4',
                              'initiative5');
        $description = array('Hoc inmaturo interitu ipse quoque sui pertaesus excessit e vita aetatis nono anno atque vicensimo cum quadriennio imperasset. natus apud Tuscos in Massa Veternensi, patre Constantio Constantini fratre imperatoris, matreque Galla sorore Rufini et Cerealis, quos trabeae consulares nobilitarunt et praefecturae.',
                                    'Haec ubi latius fama vulgasset missaeque relationes adsiduae Gallum Caesarem permovissent, quoniam magister equitum longius ea tempestate distinebatur, iussus comes orientis Nebridius contractis undique militaribus copiis ad eximendam periculo civitatem amplam et oportunam studio properabat ingenti, quo cognito abscessere latrones nulla re amplius memorabili gesta, dispersique ut solent avia montium petiere celsorum.',
                                    'Per hoc minui studium suum existimans Paulus, ut erat in conplicandis negotiis artifex dirus, unde ei Catenae inditum est cognomentum, vicarium ipsum eos quibus praeerat adhuc defensantem ad sortem periculorum communium traxit. et instabat ut eum quoque cum tribunis et aliis pluribus ad comitatum imperatoris vinctum perduceret: quo percitus ille exitio urgente abrupto ferro eundem adoritur Paulum. et quia languente dextera, letaliter ferire non potuit, iam districtum mucronem in proprium latus inpegit. hocque deformi genere mortis excessit e vita iustissimus rector ausus miserabiles casus levare multorum.',
                                    'Proinde die funestis interrogationibus praestituto imaginarius iudex equitum resedit magister adhibitis aliis iam quae essent agenda praedoctis, et adsistebant hinc inde notarii, quid quaesitum esset, quidve responsum, cursim ad Caesarem perferentes, cuius imperio truci, stimulis reginae exsertantis aurem subinde per aulaeum, nec diluere obiecta permissi nec defensi periere conplures.',
                                    'Equitis Romani autem esse filium criminis loco poni ab accusatoribus neque his iudicantibus oportuit neque defendentibus nobis. Nam quod de pietate dixistis, est quidem ista nostra existimatio, sed iudicium certe parentis; quid nos opinemur, audietis ex iuratis; quid parentes sentiant, lacrimae matris incredibilisque maeror, squalor patris et haec praesens maestitia, quam cernitis, luctusque declarat.');
        $budget = array('15000€','7520€','25420€','8460€','995€');
        $mission = array('Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                               'Lorem ipsum dolor sit amet.',
                               'Rege angustias enim facta aerarii.',
                               'Poterat iam gyris si licet.',
                               'Ita peregrini cum id haut.');
        $support = array('soutien assoRouge, assoVerte','soutien assoBleue','soutien assoJaune, soutien assoMarron','soutien assoOrange, soutien assoRose','soutien assoGrise, soutien assoIndigo');
        $street = array('1 rue sésame','5 boulevard du temps qui passe','11 allée les verts','5bis rue de la tierce','1418 avenue de verdun');
        $postcode = array('6415','1207','57029','75001','05674');
        $city = array('Arth','Genève','Venturina','Paris','Warren');
        $longitude = array("8.54","6.16","10.21","2.33","-72.85");
        $latitude = array("47.07","46.20","43.04","48.86","44.11");
        $external_skill = array('Competence Tantum voluit moverentur laudato imperator. / Competence Admissusque ultimum Caesar leviter et.',
                                      'Compétence Distortaque iniectans spadonum teneros postrema.',
                                      'Compétence Sermonem ne exposuit amicitia Scaevola. / Compétence Iungeretur societate angustum infinita ut.',
                                      'Compétence Si dederit rege usquam quidam. / Compétence His fecere saevi: sedisset specie.',
                                      'Compétence Quod tractus quod quia omnes.');

        $published = array('1','1','1','1','1');
     
        $initiative1 = new Initiative();
        $initiative1->setName($name[1]);
        $initiative1->setDescription($description[0]);
        $initiative1->setBudgetActual($budget[0]);
        $initiative1->setMission($mission[0]);
        $initiative1->setSupport($support[0]);
        $initiative1->setStreet($street[0]);
        $initiative1->setPostcode($postcode[0]);
        $initiative1->setCity($city[0]);
        $initiative1->setLongitude($longitude[0]);
        $initiative1->setLatitude($latitude[0]);
        $initiative1->setExternalSkill($external_skill[0]);
        $initiative1->setPublished($published[0]);
        $initiative1->setOrganization($this->getReference('organization1'));

        
        $initiative2 = new Initiative();
        $initiative2->setName($name[1]);
        $initiative2->setDescription($description[1]);
        $initiative2->setBudgetActual($budget[1]);
        $initiative2->setMission($mission[1]);
        $initiative2->setSupport($support[1]);
        $initiative2->setStreet($street[1]);
        $initiative2->setPostcode($postcode[1]);
        $initiative2->setCity($city[1]);
        $initiative2->setLongitude($longitude[1]);
        $initiative2->setLatitude($latitude[1]);
        $initiative2->setExternalSkill($external_skill[1]);
        $initiative2->setPublished($published[1]);
        $initiative2->setOrganization($this->getReference('organization2'));

        $initiative3 = new Initiative();
        $initiative3->setName($name[2]);
        $initiative3->setDescription($description[2]);
        $initiative3->setBudgetActual($budget[2]);
        $initiative3->setMission($mission[2]);
        $initiative3->setSupport($support[2]);
        $initiative3->setStreet($street[2]);
        $initiative3->setPostcode($postcode[2]);
        $initiative3->setCity($city[2]);
        $initiative3->setLongitude($longitude[2]);
        $initiative3->setLatitude($latitude[2]);
        $initiative3->setExternalSkill($external_skill[2]);
        $initiative3->setPublished($published[2]);
        $initiative3->setOrganization($this->getReference('organization1'));

        
        $initiative4 = new Initiative();
        $initiative4->setName($name[3]);
        $initiative4->setDescription($description[3]);
        $initiative4->setBudgetActual($budget[3]);
        $initiative4->setMission($mission[3]);
        $initiative4->setSupport($support[3]);
        $initiative4->setStreet($street[3]);
        $initiative4->setPostcode($postcode[3]);
        $initiative4->setCity($city[3]);
        $initiative4->setLongitude($longitude[3]);
        $initiative4->setLatitude($latitude[3]);
        $initiative4->setExternalSkill($external_skill[3]);
        $initiative4->setPublished($published[3]);
        $initiative4->setOrganization($this->getReference('organization2'));


        $manager->persist($initiative1);
        $manager->persist($initiative2);
        $manager->persist($initiative3);
        $manager->persist($initiative4);

        $manager->flush();

    }
    public function getOrder()
    {
        return 5;
    }
}