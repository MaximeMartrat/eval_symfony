<?php

namespace App\Controller;

use App\Entity\Manager;
use App\Repository\ManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\DataFixtures\ManagerFixture;

class ManagerController extends AbstractController
{
    #[Route('/manager', name: 'app_manager')]
    public function allManagers(Request $request, ManagerRepository $managerRepository): Response
    {
        return $this->render('manager/index.html.twig', [
            'controller_name' => 'ManagerController',
            'managers' => $managerRepository->findAll(),
        ]);
    }

    

    #[Route('/managerSet', name: 'app_manager_set')]

    public function reloadEquipeManager(EntityManagerInterface $entityManager, ManagerRepository $managerRepository):Response
    {   
        $entityManager->createQuery('UPDATE App\Entity\Manager m SET m.manager_equipe = null')
                      ->execute();
                      return $this->redirectToRoute('app_equipe_new');
    }
}
