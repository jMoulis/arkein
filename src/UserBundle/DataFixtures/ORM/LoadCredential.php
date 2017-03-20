<?php
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class LoadCredential implements  \Doctrine\Common\DataFixtures\FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(
            __DIR__.'/credential.yml',
            $manager,
        [
            'providers' => [$this]
        ]
            );
    }
    public function bcrypt()
    {
        $passbcrypt = '$2y$10$ZYW4Qggu3n699i9rS6FxN.MhJTbpj/gNYCPWbSHZsbftotuARr7vq';

        return $passbcrypt;

    }

}