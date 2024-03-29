<?php

namespace App\Controller\Admin;

use App\Entity\Devis;
use App\Entity\Operation;
use App\Entity\TypeOperation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard', methods: ['GET'])]
    public function CA(EntityManagerInterface $entityManager)
    {

        // Récupérer toutes les opérations et types d'opération
        $operations = $entityManager->getRepository(Operation::class)->findAll();
        $types = $entityManager->getRepository(TypeOperation::class)->findAll();

        // Initialiser un tableau pour stocker le chiffre d'affaires par type d'opération
        $chiffreAffairesParType = [];

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


        // Rendre la vue avec les données calculées
        return $this->render('admin/dashboard/index.html.twig', [
            'chiffreAffairesParType' => $chiffreAffairesParType,
            'chiffreAffaire' => $chiffreAffaire
        ]);
    }


}
