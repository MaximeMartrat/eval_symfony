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
    #[Route('/symfoot', name: 'app_foot')]
    public function index(): Response
    {
        return $this->render('equipe/index.html.twig');
    }

    #[Route('/symfoot/deconnect', name: 'deconnect_foot')]
    public function deconnect(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('joueur');
        return $this->render('equipe/index.html.twig');
    }

    #[Route('/equipeAccueil', name: 'acceuil_equipe')]
    public function equipeAccueil(Request $request): Response
    {
        $form = $this->createForm(EquipeFormType::class);
        $form->handleRequest($request);
            return $this->render('equipe/form_equipe.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/createEquipe', name: 'app_create')]
    public function createEquipe(Request $request, EquipeRepository $equipeRepository, EntityManagerInterface $entityManager, SessionInterface $session):Response
    {
        // Vérifiez si une équipe est déjà enregistrée dans la session
        $session = $request->getSession();
        $equipe = $session->get('equipe');
        if (!$equipe) {
            $equipe = new Equipe();
        }
        $managers = $session->get('manager');
        $joueurs = $session->get('joueur');
        foreach($managers as $manager) {
            $equipe->addManager($manager);
        }   
        foreach($joueurs as $joueur) {
            $equipe->addJoueur($joueur);
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
                // L'équipe est enregistrée avec succès, je la supprime de la session
                $session->remove('equipe');
                return $this->render('equipe/message.html.twig', [
                    'message' => 'equipe créée',
                ]);
            } else {
                // L'équipe n'est pas encore complète, je l'enregistre dans la session
                $session->set('equipe', $equipe);
            }
        }

        return $this->render('equipe/form_equipe.html.twig', [
            'form' => $form->createView(),
        ]);

    }
    
    #[Route('/createEquipe/manager', name: 'manager_create')]

    public function addManagerEquipe(Request $request, ManagerRepository $managerRepository, JoueurRepository $joueurRepository, EntityManagerInterface $entityManager, SessionInterface $session):Response
    {
        $session = $request->getSession();
        if($request->isXmlHttpRequest()) {
            $managersId = $request->request->all("managers");
            $managers = $entityManager
            ->getRepository(Manager::class)
            ->findBy(['id' => $managersId]);
            $session->set('manager', $managers);
        }

        if(isset($managers) && (count($managers) === 5)) {
            return $this->render('equipe/joueur_form.html.twig', [
                'joueurs' => $joueurRepository->findAll(),

            ]);
        }

        return $this->render('equipe/manager_form.html.twig', [
            'managers' => $managerRepository->findAll(),
        ]);
    }

    #[Route('/createEquipe/joueur', name: 'joueur_create')]

    public function addJoueurEquipe(Request $request, JoueurRepository $joueurRepository, SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $session = $request->getSession();
        if($request->isXmlHttpRequest()) {
            $joueursId = $request->request->all('joueurs');
            $joueurs = $entityManager
                    ->getRepository(Joueur::class)
                    ->findBy(['id' => $joueursId]);
            $session->set('joueur', $joueurs);
        }

        if(isset($joueurs) && (count($joueurs) === 22)) {
            return $this->render('equipe/joueur_form.html.twig', [
                'joueurs' => $joueurRepository->findAll(),

            ]);
        }    
        
        return $this->render('equipe/joueur_form.html.twig', [
            'joueurs' => $joueurRepository->findAll(),
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
