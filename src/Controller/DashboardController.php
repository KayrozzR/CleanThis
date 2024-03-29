<?php

namespace App\Controller;

use App\Entity\TypeOperation;
use App\Repository\OperationRepository;
use App\Repository\TypeOperationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $typeOperations = $entityManager->getRepository(TypeOperation::class)->findAll();

        $typeOpPrice = [];
        foreach ($typeOperations as $typeOperation) {
            $typeOpPrice[] = $typeOperation->getTarif();
        }

        $userData = [];
        foreach ($users as $user) {
            $operations = $user->getOperations(); // Récupérer les opérations associées à cet utilisateur

            $userOpEnd = 0;
            $userOpInProg = 0;

            foreach ($operations as $operation) {
                if ($operation->isStatusOperation()) {
                    $userOpEnd++;
                } else {
                    $userOpInProg++;
                }
            }

            $userData[$user->getId()] = [
                'userName' => $user->getLastname(),
                'userOpEnd' => $userOpEnd,
                'userOpInProg' => $userOpInProg
            ];
        }

        return $this->render('admin/dashboard/index.html.twig', [
            'userData' => json_encode($userData),
            'typeOpPrice' => json_encode($typeOpPrice)
        ]);
    }
}

