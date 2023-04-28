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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class EquipeController extends AbstractController
{
    //route pour afficher l'acceuil
    #[Route('/symfoot', name: 'app_foot')]
    public function index(): Response
    {
        return $this->render('equipe/index.html.twig');
    }

    //route pour effacer les sessions
    #[Route('/symfoot/deconnect', name: 'deconnect_foot')]
    public function deconnect(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('joueur');
        $session->remove('manager');
        $session->remove('equipe');
        return $this->render('equipe/index.html.twig');
    }

    //route pour afficher l'acceuil de la création d'equipe
    #[Route('/equipeAccueil', name: 'acceuil_equipe')]
    public function equipeAccueil(Request $request): Response
    {
        $form = $this->createForm(EquipeFormType::class);
        $form->handleRequest($request);
            return $this->render('equipe/form_equipe.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //route pour créer une equipe
    #[Route('/setEquipe', name: 'app_set')]
    public function setEquipe(Request $request, EquipeRepository $equipeRepository, EntityManagerInterface $entityManager, SessionInterface $session):Response
    {
        // Vérifiez si une équipe est déjà enregistrée dans la session
        $session = $request->getSession();
        $myequipe = $session->get('equipe');
        $equipeId = $myequipe->getId();
        $equipe = $entityManager
                ->getRepository(Equipe::class)
                ->find($equipeId);    
        if(count($equipe->getJoueurs()) > 15 && count($equipe->getManagers()) > 4) {
            $equipe->setBudget();
            $equipe->setRenommee();
            $entityManager->persist($equipe);
            $entityManager->flush();
            // L'équipe est enregistrée avec succès, je la supprime de la session
            $session->remove('equipe');
            return $this->render('equipe/message.html.twig', [
                'message' => 'equipe créée',
            ]);
        } else {
            // L'équipe n'est pas encore complète, je l'enregistre dans la session
            $session->set('equipe', $equipe);
            return $this->render('equipe/index.html.twig');
        }
    }

    
    //route pour enregistrer l'equipe
    #[Route('/createEquipe', name: 'app_create')]
    public function createEquipe(Request $request, EquipeRepository $equipeRepository, ManagerRepository $managerRepository, EntityManagerInterface $entityManager, SessionInterface $session):Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeFormType::class, $equipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $equipe = $form->getData();
            $entityManager->persist($equipe);
            $entityManager->flush();
            $session->set('equipe', $equipe);
            return $this->render('equipe/manager_form.html.twig', [
                'managers' => $managerRepository->findAll(),
            ]);
        }
        return $this->render('equipe/form_equipe.html.twig', [
            'form' => $form->createView(),
        ]);


    }

    //route pour créer le staff manager
    #[Route('/createEquipe/manager', name: 'manager_create')]

    public function addManagerEquipe(Request $request, ManagerRepository $managerRepository, JoueurRepository $joueurRepository, EntityManagerInterface $entityManager, SessionInterface $session):Response
    {
        $session = $request->getSession();
        $myequipe = $session->get('equipe');
        $equipeId = $myequipe->getId();
        $equipe = $entityManager
                ->getRepository(Equipe::class)
                ->find($equipeId);
        if($request->getMethod() == 'POST') {
            $managersId = $request->request->all("manager");
            dump($managersId);
            $managers = $entityManager
                ->getRepository(Manager::class)
                ->findBy(['id' => $managersId]);
            if (isset($managers)) {
                foreach($managers as $manager) {
                    $equipe->addManager($manager);
                    $equipe->setBudget();
                    $entityManager->persist($equipe);
                    $entityManager->flush();
                }
                return $this->render('equipe/joueur_form.html.twig', [
                    'joueurs' => $joueurRepository->findAll(),
                ]);   
            }
        }
        return $this->render('equipe/manager_form.html.twig', [
            'managers' => $managerRepository->findAll(),
        ]);
    }

    //route pour créer le staff joueur
    #[Route('/createEquipe/joueur', name: 'joueur_create')]

    public function addJoueurEquipe(Request $request, JoueurRepository $joueurRepository, SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $session = $request->getSession();
        $myequipe = $session->get('equipe');
        $equipeId = $myequipe->getId();
        $equipe = $entityManager
                ->getRepository(Equipe::class)
                ->find($equipeId);
        if($request->getMethod() == 'POST') {
            $joueursId = $request->request->all('joueur');
            $joueurs = $entityManager
                    ->getRepository(Joueur::class)
                    ->findBy(['id' => $joueursId]);
            if(isset($joueurs)) {
                foreach($joueurs as $joueur) {
                    $equipe->addJoueur($joueur);
                    $equipe->setBudget();
                    $equipe->setRenommee();
                    $entityManager->persist($equipe);
                    $entityManager->flush();
                }
                return $this->redirectToRoute('app_set');   
            }
        }  
        return $this->render('equipe/joueur_form.html.twig', [
            'joueurs' => $joueurRepository->findAll(),
        ]);
    }

    //route pour afficher le menu de sélection d'une equipe
    #[Route('/equipe', name: 'app_equipe')]
    public function allEquipe(Request $request, EquipeRepository $equipeRepository): Response
    {
        return $this->render('equipe/all_equipe.html.twig', [
            'controller_name' => 'EquipeController',
            'equipes' => $equipeRepository->findAll(),
        ]);
    }
    
    //route pour reload les equipes dans la base de données
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
