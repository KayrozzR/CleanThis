<?php

namespace App\Controller;

use App\Entity\TypeOperation;
use App\Repository\UserRepository;
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
        // $typeOperations = $entityManager->getRepository(TypeOperation::class)->findAll();

        // $typeOpPrice = [];

        // foreach ($typeOperations as $typeOperation) {
        //     $typeOpPrice[] = $typeOperation->getTarif();
        // }

        
        $desiredRoles = ['ROLE_ADMIN', 'ROLE_SENIOR', 'ROLE_APPRENTI'];
        $usersEmployee = [];
        $totalTarifs = [];

        foreach ($users as $user) {
            foreach ($user->getRoles() as $role) {
                if (in_array($role, $desiredRoles)) {
                    $operations = $user->getOperations();
                    foreach ($operations as $operation) {
                        $devis = $operation->getDevis();
                        foreach ($devis as $devi) {
                            $tarif = $devi->getTypeOperation()->getTarif();
                            if (!isset($totalTarifs[$user->getLastname()])) {
                                $totalTarifs[$user->getLastname()] = 0;
                            }
                            $totalTarifs[$user->getLastname()] += $tarif;
                        }
                    }
                    break;
                }
            }
        }

        foreach ($users as $user) {
            foreach ($user->getRoles() as $role) {
                if (in_array($role, $desiredRoles)) {
                    $usersEmployee[] = $user->getLastname();
                    break;
                }
            }
        }

        return $this->render('admin/dashboard/index.html.twig', [
            'usersEmployee' => json_encode($usersEmployee),
            'users' => $users,
            'totalTarifs' => json_encode($totalTarifs)
        ]);
    }
}

