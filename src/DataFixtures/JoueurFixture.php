<?php

namespace App\DataFixtures;

use App\Entity\Joueur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class JoueurFixture extends Fixture
{

    private $faker;

    public function __construct() {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        $count = 260;
        for($i = 0; $i < $count; $i++) {
            //noms aléatoires
            $noms = $this->faker->lastName();
            //prenoms aléatoires
            $prenoms = $this->faker->firstName($gender = 'male');
            //positions aléatoires
            if($i <= 70) {
                $position = 'Avant'; 
            } elseif($i > 70 && $i <= 160) {
                $position = 'Milieu';
            } elseif($i > 160 && $i <= 230) {
                $position = 'Arrière';
            } else {
                $position = 'Goal';
            }

            //vitesse aleatoire
            $vitesses = 
            $this->faker->numberBetween(30, 100);
            //dribble aleatoire
            $dribbles = $this->faker->numberBetween(30, 100);
            //tir aléatoire
            $tirs = $this->faker->numberBetween(30, 100);
            //renommee aléatoire
            $renommees = $this->faker->numberBetween(30, 100);
            //salaire aléatoire
            $salaires = $this->faker->randomNumber(6, true);
            //arret aleatoire
            $arrets = $this->faker->numberBetween(30, 100);
            $joueur = new Joueur();
            $joueur->setNom($noms);
            $joueur->setPrenom($prenoms);
            $joueur->setPosition($position);
            $joueur->setVitesse($vitesses);
            $joueur->setDribble($dribbles);
            $joueur->setTir($tirs);
            $joueur->setRenommee($renommees);
            $joueur->setSalaire($salaires);
            if ($joueur->getPosition() === 'Goal') {
                $joueur->setArret($arrets);
            }
    
            // $product = new Product();
            // $manager->persist($product);
            $manager->persist($joueur);
            $manager->flush();

        }
    }
}
