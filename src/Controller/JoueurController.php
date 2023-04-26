<?php

namespace App\Controller;
use App\Entity\Joueur;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\DataFixtures\JoueurFixture;

class JoueurController extends AbstractController
{
    #[Route('/joueur', name: 'app_joueur')]
    public function AllJoueurs(Request $request, JoueurRepository $joueurRepository): Response
    {
        return $this->render('joueur/index.html.twig', [
            'controller_name' => 'JoueurController',
            'joueurs' => $joueurRepository->findAll(),
        ]);
    }

    #[Route('/joueurSet', name: 'app_joueur_set')]

    public function reloadEquipeJoueur(EntityManagerInterface $entityManager, JoueurFixture $joueurFixture, JoueurRepository $joueurRepository):Response
    {   
        $entityManager->createQuery('UPDATE App\Entity\Joueur j SET j.joueur_equipe = null')
                      ->execute();
                      return $this->redirectToRoute('app_manager_set');
    }

    #[Route('/joueur/{id}', name: 'app_joueur_id')]

    public function JoueurById(Request $request, JoueurRepository $joueurRepository, $id): Response
    {
        return $this->render('joueur/index.html.twig', [
            'controller_name' => 'JoueurController',
            'joueur' => $joueurRepository->find($id),
        ]);
    }
}
