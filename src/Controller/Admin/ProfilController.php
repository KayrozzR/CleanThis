<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/profil', name: 'app_admin_profil')]
    public function index(): Response
    {
        return $this->render('admin/profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    #[Route('/{id}/edit/profil', name: 'app_user_edit_profil', methods: ['POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le nom du champ à mettre à jour depuis la requête AJAX
        $requestData = json_decode($request->getContent(), true);
        $fieldName = key($requestData);
    
        // Récupérer la nouvelle valeur du champ depuis la requête AJAX
        $newFieldValue = $requestData[$fieldName];
    
        // Vérifier si le champ à mettre à jour existe dans l'entité User
        if (!property_exists(User::class, $fieldName)) {
            return new JsonResponse(['error' => 'Champ invalide'], 400);
        }
    
        // Mettre à jour le champ approprié de l'utilisateur
        $setterMethod = 'set' . ucfirst($fieldName);
        $user->$setterMethod($newFieldValue);
    
        // Sauvegarder les modifications en base de données
        $entityManager->flush();
    
        // Renvoyer une réponse JSON pour indiquer que les modifications ont été enregistrées avec succès
        return new JsonResponse(['success' => true]);
    }
    #[Route('/save-password', name: 'app_save_password', methods: ['POST'])]
    public function savePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer les données envoyées depuis la requête AJAX
        $requestData = json_decode($request->getContent(), true);
        $currentPassword = $requestData['currentPassword'];
        $newPassword = $requestData['newPassword'];
    
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();
    
        // Vérifier que le mot de passe actuel est correct
        if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
            return new JsonResponse(['error' => 'Le mot de passe actuel est incorrect'], 400);
        }
    
        // Mettre à jour le mot de passe de l'utilisateur avec le nouveau mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
    
        // Sauvegarder les modifications en base de données
        $this->entityManager->flush();
    
        // Renvoyer une réponse JSON pour indiquer que les modifications ont été enregistrées avec succès
        return new JsonResponse(['success' => true]);
    }

    #[Route("/upload-avatar", name:"upload_avatar", methods:['POST'])]
    
    public function uploadAvatar(Request $request): Response
    {
        // Récupérer le fichier envoyé
        $file = $request->files->get('avatar');

        if ($file) {
            // Générer un nom unique pour le fichier
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Spécifier le répertoire où vous souhaitez stocker les avatars
            $directory = $this->getParameter('kernel.project_dir') . '/public/uploads/avatars';

            // Déplacer le fichier vers le répertoire spécifié
            $file->move($directory, $fileName);

            // Mettre à jour l'avatar de l'utilisateur
            $user = $this->getUser();
            $user->setAvatar('uploads/avatars/' . $fileName);

            // Get the EntityManager
            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection vers la page où vous affichez l'utilisateur
            return $this->redirectToRoute('app_admin_profil');
        }

        // Si aucun fichier n'a été envoyé, afficher un message d'erreur ou gérer selon vos besoins
        // ...

        // Si vous avez besoin de retourner une réponse différente
        // return new Response('Erreur lors du téléchargement de l\'avatar', 400);
    }
    }
    
