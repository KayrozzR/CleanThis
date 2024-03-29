<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Entity\TypeOperation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {

        // Récupérer toutes les opérations et types d'opération
        $operations = $entityManager->getRepository(Operation::class)->findAll();
        $types = $entityManager->getRepository(TypeOperation::class)->findAll();
        $users = $userRepository->findAll();
        // $typeOperations = $entityManager->getRepository(TypeOperation::class)->findAll();

        // $typeOpPrice = [];

        // foreach ($typeOperations as $typeOperation) {
        //     $typeOpPrice[] = $typeOperation->getTarif();
        // }

        // Initialiser un tableau pour stocker le chiffre d'affaires par type d'opération
        $chiffreAffairesParType = [];
        $desiredRoles = ['ROLE_ADMIN', 'ROLE_SENIOR', 'ROLE_APPRENTI'];
        $usersEmployee = [];
        $totalTarifs = [];

        // Parcourir les types d'opération
        foreach ($types as $type) {
            $typeId = $type->getId();
            $chiffreAffaires = 0;

            // Parcourir les opérations
            foreach ($operations as $operation) {

                if ($operation->isStatusOperation()) {
                    
                    // Récupérer tous les devis associés à cette opération
                    $devisCollection = $operation->getDevis();
                    foreach ($devisCollection as $devis) {
                        // Vérifie si le type d'opération du devis correspond au type en cours de traitement
                        if ($devis->getTypeOperation()->getId() === $typeId) {
                            // Ajoute le tarif du devis au chiffre d'affaires du type d'opération
                            $chiffreAffaires += $devis->getTypeOperation()->getTarif();
                        }
                    }
                }

            }

            // Stocke le chiffre d'affaires calculé pour ce type d'opération
            $chiffreAffairesParType[$type->getLibelle()] = $chiffreAffaires;
        }

        //Calcul du CA
         // Initialiser une variable pour stocker le chiffre d'affaire totale
         $chiffreAffaire = 0;
 
             // Parcourir les opérations
             foreach ($operations as $operation) {
                 if ($operation->isStatusOperation()) {
                     
                     // Récupérer tous les devis associés à cette opération
                     $devisCollection = $operation->getDevis();
                     foreach ($devisCollection as $devis) {
                             // Ajouter le tarif du devis au chiffre d'affaires du type d'opération
                             $chiffreAffaire += $devis->getTypeOperation()->getTarif();
                                                    
                     }
                 }
                }

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
            'chiffreAffairesParType' => $chiffreAffairesParType,
            'chiffreAffaire' => $chiffreAffaire,
            'totalTarifs' => json_encode($totalTarifs)
        ]);
    }
}