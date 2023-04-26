<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Joueur;
use App\Entity\Manager;
use App\Form\EquipeFormType;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use App\Repository\ManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\DataFixtures\EquipeFixture;


class EquipeController extends AbstractController
{
    #[Route('/symfoot', name: 'app_foot')]
    public function index(): Response
    {
        return $this->render('equipe/index.html.twig');
    }

    #[Route('/createEquipe', name: 'app_create')]
    public function createEquipe(Request $request, EquipeRepository $equipeRepository, JoueurRepository $joueurRepository, ManagerRepository $managerRepository, EntityManagerInterface $entityManager):Response
    {
        // Vérifiez si une équipe est déjà enregistrée dans la session
        $session = $request->getSession();
        $equipe = $session->get('equipe');
        if (!$equipe) {
            $equipe = new Equipe();
        }

        $joueursId = $request->request->get('joueurs');
        $managersId = $request->request->get('managers');
        $joueurs = $entityManager
                    ->getRepository(Joueur::class)
                    ->findBy(['id' => $joueursId]);
        $managers = $entityManager
                    ->getRepository(Manager::class)
                    ->findBy(['id' => $managersId]);

        foreach($joueurs as $joueur) {
            $equipe->addJoueur($joueur);
        }
        foreach($managers as $manager) {
            $equipe->addManager($manager);
        }

        $form = $this->createForm(EquipeFormType::class, $equipe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $equipe = $form->getData();
            if(count($equipe->getJoueurs()) > 15 && count($equipe->getManagers()) > 4) {
                $equipe->setBudget();
                $equipe->setRenommee();
                $entityManager->persist($equipe);
                $entityManager->flush();
                // L'équipe est enregistrée avec succès, supprimez-la de la session
                $session->remove('equipe');
                return $this->render('equipe/message.html.twig', [
                    'message' => 'equipe créée',
                ]);
            } else {
                // L'équipe n'est pas encore complète, enregistrez-la dans la session
                $session->set('equipe', $equipe);
            }
        }

        return $this->render('equipe/form_equipe.html.twig', [
            'joueurs' => $joueurRepository->findAll(),
            'managers' => $managerRepository->findAll(),
            'form' => $form->createView(),
        ]);

    }
    

    #[Route('/equipe', name: 'app_equipe')]
    public function allEquipe(Request $request, EquipeRepository $equipeRepository): Response
    {
        return $this->render('equipe/all_equipe.html.twig', [
            'controller_name' => 'EquipeController',
            'equipes' => $equipeRepository->findAll(),
        ]);
    }
    
    #[Route('/equipereload', name: 'app_equipe_new')]
    public function newEquipe(EntityManagerInterface $entityManager, EquipeFixture $equipeFixture, EquipeRepository $equipeRepository): Response
    {
        $entityManager->createQuery('DELETE FROM App\Entity\Equipe')
                      ->execute();
                      $equipeFixture->load($entityManager);
        
        return $this->render('equipe/index.html.twig', [
            'controller_name' => 'EquipeController',
            'equipes' => $equipeRepository->findAll(),
        ]);
    }

}
