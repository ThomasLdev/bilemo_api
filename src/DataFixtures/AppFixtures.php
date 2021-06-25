<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Phone;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $phone = new Phone();
        $phone->setSku('XS25669-28');
        $phone->setDescription('Lorem ipsum une desc bidon');
        $phone->setHeight(100.3);
        $phone->setWeight(400);
        $phone->setWidth(50.6);
        $phone->setPrice(450);
        $manager->persist($phone);

        $client = new Client();
        $client->setBrand('FSR');
        $client->setCountry('FR');
        $client->setPhoneNumber('0565256323');
        $client->setEmailAddress('commercial@fsr.fr');
        $manager->persist($client);

        $user = new User();
        $user->setFirstName('Cindy');
        $user->setLastName('Crowford');
        $user->setEmailAddress('cindy.crowford@gmail.com');
        $user->setPhoneNumber('0645625363');
        $user->setClient($client);
        $manager->persist($user);

        $manager->flush();
    }
}
