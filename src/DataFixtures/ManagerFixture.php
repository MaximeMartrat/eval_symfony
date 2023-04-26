<?php

namespace App\DataFixtures;

use App\Entity\Manager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ManagerFixture extends Fixture
{
    private $faker;

    public function __construct() {
        $this->faker = Factory::create();
    }
    public function load(ObjectManager $objetmanager): void
    {
        for($i =0; $i < 100; $i++) {
            //nom aléatoire
            $noms = $this->faker->lastName();
            //prenom aléatoire
            $prenoms = $this->faker->firstName();
            //role aléatoire
            if($i <= 25) {
                $role = 'Entraineur';
            } elseif($i > 25 && $i <= 50) {
                $role = 'DG';
            } elseif ($i > 50 && $i <= 75) {
                $role = 'DS';
            } else {
                $role = 'DM';
            }
            //salaire aléatoire
            $salaires = $this->faker->randomNumber(6, true);
            $manager = new Manager();
            $manager->setNom($noms);
            $manager->setPrenom($prenoms);
            $manager->setRole($role);
            $manager->setSalaire($salaires);
            $objetmanager->persist($manager);
            $objetmanager->flush();
        }
    }
}
