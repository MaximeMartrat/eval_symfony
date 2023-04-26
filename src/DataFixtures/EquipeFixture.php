<?php

namespace App\DataFixtures;

use App\Entity\Joueur;
use App\Entity\Manager;
use App\Entity\Equipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EquipeFixture extends Fixture
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $objetManager): void
    {
    
        $joueurs = $objetManager
            ->getRepository(Joueur::class)
            ->findAll();
        
        shuffle($joueurs);
        
        $managers = $objetManager
            ->getRepository(Manager::class)
            ->findAll();
    
        shuffle($managers);

        for($i = 0; $i < 9; $i++) {

            $noms = $this->faker->word();
            $ville = $this->faker->city();
            
            $equipe = new Equipe();
            
            $equipe->setNom($noms);
            $equipe->setVille($ville);

            foreach ($joueurs as $joueur) {
                if(count($equipe->getJoueurs()) < 22 && $joueur->getJoueurEquipe() === null) {

                    if($equipe->countJoueursByPosition("Goal") < 2 && $joueur->getPosition() === 'Goal') {
                        $equipe->addJoueur($joueur);
                        $joueur->setJoueurEquipe($equipe);
                    }
                    if($equipe->countJoueursByPosition("Avant") < 6 && $joueur->getPosition() === 'Avant') {
                        $equipe->addJoueur($joueur);
                        $joueur->setJoueurEquipe($equipe);
                    }
                    if($equipe->countJoueursByPosition("Milieu") < 8 && $joueur->getPosition() === 'Milieu') {
                        $equipe->addJoueur($joueur);
                        $joueur->setJoueurEquipe($equipe);
                    }
                    if($equipe->countJoueursByPosition("Arrière") < 6 && $joueur->getPosition() === 'Arrière') {
                        $equipe->addJoueur($joueur);
                        $joueur->setJoueurEquipe($equipe);
                    }                       
                }
            }
            foreach($managers as $manager) {
                if(count($equipe->getManagers()) < 5 && $manager->getManagerEquipe() === null) {

                    if($manager->getRole() === 'Entraineur' && $equipe->countManagersByRole("Entraineur") < 2) {
                        $equipe->addManager($manager);
                        $manager->setManagerEquipe($equipe);
                    }
                    if($manager->getRole() === 'DG' && $equipe->countManagersByRole("DG") < 1) {
                        $equipe->addManager($manager);
                        $manager->setManagerEquipe($equipe);
                    }
                    if($manager->getRole() === 'DS' && $equipe->countManagersByRole("DS") < 1) {
                        $equipe->addManager($manager);
                        $manager->setManagerEquipe($equipe);
                    }
                    if($manager->getRole() === 'DM' && $equipe->countManagersByRole("DM") < 1) {
                        $equipe->addManager($manager);
                        $manager->setManagerEquipe($equipe);
                    }
                } 
            }
            $equipe->setRenommee();
            $equipe->setBudget();
            $objetManager->persist($equipe);
        }
        $objetManager->flush();
    }
    
}

