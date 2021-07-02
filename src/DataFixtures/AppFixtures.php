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
        $phoneNames = ['Xveria-S3', 'Xveria-S4', 'Xveria-S5', 'Xveria-S8', 'Xveria-A6', 'Xveria-A7'];
        $clientNames = ['FSR', 'Souygues', 'Strawberry', 'Orage'];
        $inputFirstNames = ['Cindy', 'John', 'Laura', 'Sylvie', 'Marc'];
        $inputLastNames = ['Smith', 'Sunflower', 'Cartier', 'Koolher', 'Barker'];

        foreach ($phoneNames as $phoneName) {
            $phone = new Phone();
            $phone->setSku('XS-'.$phoneName);
            $phone->setDescription('
        Voici le smartphone qui redéfinit l’avenir. Découvrez son écran pliable et sa charnière révolutionnaire. Ces deux innovations spectaculaires vous permettent de l’ouvrir comme un livre, de le maintenir plié selon l’angle souhaité, et de réinventer les codes du design mobile. Découvrez le '.$phoneName.'. Un appareil photo parfaitement intégré pour révolutionner la photographie. Prenez des vidéos en 8K dignes d’un cinéma et capturez des images fixes à couper le souffle.');
            $phone->setHeight(rand(80,150));
            $phone->setWeight(rand(250, 400));
            $phone->setWidth(rand(50, 80));
            $phone->setPrice(rand(290, 950));
            $manager->persist($phone);
        }

        foreach($clientNames as $clientName) {
            $client = new Client();
            $client->setBrand($clientName);
            $client->setCountry('FR');
            $client->setPhoneNumber('06'.rand(1, 9).'314983'.rand(1, 9));
            $client->setEmailAddress('commercial@'.$clientName.'.fr');
            $manager->persist($client);
        }

        for($i=0; $i<5; $i++) {
            $user = new User();
            $user->setFirstName(array_rand(array_flip($inputFirstNames)));
            $user->setLastName(array_rand(array_flip($inputLastNames)));
            $user->setEmailAddress(''.$user->getFirstName().'.'.$user->getLastName().'@email.com');
            $user->setPhoneNumber('06'.rand(1, 9).'814253'.rand(1, 9));
            $user->setClient($client);
            $user->addPhone($phone);
            $manager->persist($user);
        }

        $manager->flush();
    }
}

