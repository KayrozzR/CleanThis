<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Operation;
use App\Entity\TypeOperation;
use App\Service\PostLogsService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    private PostLogsService $postLogsService;

    public function __construct(PostLogsService $postLogsService)
    {
        $this->postLogsService = $postLogsService;
    }
    #[Route('/dashboard', name: 'app_admin_dashboard', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, UserRepository $userRepository, TokenInterface $token, PostLogsService $postLogsService): Response
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
                            $chiffreAffaires += $devis->getTarifCustom();
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
                             $chiffreAffaire += $devis->getTarifCustom();
                                                    
                     }
                 }
                }



                 //calcul du nombre d'opérations terminées:
             $nombreOperationsTermine= 0;
             // Parcourir les opérations
          foreach ($operations as $operation) {
              //Si l'opération a un status términé:
              if ($operation->isStatusOperation()) {
                      //On incrémente le nombre d'opération
                      $nombreOperationsTermine++;
              }
          }

            //calcul du nombre d'opérations total:
            $nombreOperations= 0;
            // Parcourir les opérations
          foreach ($operations as $operation) {
                      //On incrémente le nombre d'opération
                      $nombreOperations++;   
          }


            // Calcul du nombre de clients
            $clients = 0;

            // Parcourir les utilisateurs
            foreach ($users as $user) {
                // Vérifier si le rôle "ROLE_CLIENT" est présent dans les rôles de l'utilisateur
                if (in_array("ROLE_CLIENT", $user->getRoles(), true)) {
                    // Incrémenter le nombre de clients
                    $clients++;
                }
            }

        foreach ($users as $user) {
            foreach ($user->getRoles() as $role) {
                if (in_array($role, $desiredRoles)) {
                    $operations = $user->getOperations();
                    foreach ($operations as $operation) {
                        if ($operation->isStatusOperation()) {
                            $devis = $operation->getDevis();
                            foreach ($devis as $devi) {
                                $tarif = $devi->getTarifCustom();
                                if (!isset($totalTarifs[$user->getLastname()])) {
                                    $totalTarifs[$user->getLastname()] = 0;
                                }
                                $totalTarifs[$user->getLastname()] += $tarif;
                            }
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

        // $now= new DateTimeImmutable();
        // $tt = $now->format('Y-m-d H:i:s');
        // $user=$token->getUser(); 
        // $logData = [
        //     'EventTime' => $tt,
        //     'LoggerName' => 'CAType',
        //     'User' => $chiffreAffairesParType, // Vous pouvez utiliser le nom d'utilisateur ou toute autre information pertinente
        //     'Message' => 'Montant',
        //     'Level' => 'INFO',
        //     'Data' => 'Chiffre d\'affaire par Type',
        // ];


        // try {
        //     $postLogsService->postLogs($logData);
        // } catch (\Exception $e) {
        //     // Gérer les erreurs si la requête échoue
        //     echo ("Erreur");
        // };

        // $now= new DateTimeImmutable();
        // $tt = $now->format('Y-m-d H:i:s');
        // $user=$token->getUser(); 
        // $logData = [
        //     'EventTime' => $tt,
        //     'LoggerName' => 'CA',
        //     'User' => $chiffreAffaires, // Vous pouvez utiliser le nom d'utilisateur ou toute autre information pertinente
        //     'Message' => 'Statistique',
        //     'Level' => 'INFO',
        //     'Data' => 'Chiffre d\'affaire',
        // ];


        // try {
        //     $postLogsService->postLogs($logData);
        // } catch (\Exception $e) {
        //     // Gérer les erreurs si la requête échoue
        //     echo ("Erreur");
        // };


        return $this->render('admin/dashboard/index.html.twig', [
            'usersEmployee' => json_encode($usersEmployee),
            'users' => $users,
            'chiffreAffairesParType' => $chiffreAffairesParType,
            'chiffreAffaire' => $chiffreAffaire,
            'nombreOperationsTermine'=> $nombreOperationsTermine,
            'nombreOperations' => $nombreOperations,
            'clients' => $clients,
            'totalTarifs' => json_encode($totalTarifs)
        ]);
    }
}