<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\DevisRepository;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $requestData = json_decode($request->getContent(), true);
        $fieldName = key($requestData);

        $newFieldValue = $requestData[$fieldName];

        if (!property_exists(User::class, $fieldName)) {
            return new JsonResponse(['error' => 'Champ invalide'], 400);
        }

        $setterMethod = 'set' . ucfirst($fieldName);
        $user->$setterMethod($newFieldValue);

        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
    #[Route('/save-password', name: 'app_save_password', methods: ['POST'])]
    public function savePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $requestData = json_decode($request->getContent(), true);
        $currentPassword = $requestData['currentPassword'];
        $newPassword = $requestData['newPassword'];

        $user = $this->getUser();

        if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
            return new JsonResponse(['error' => 'Le mot de passe actuel est incorrect'], 400);
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->flush();


        return new JsonResponse(['success' => true]);
    }

    #[Route("/upload-avatar", name: "upload_avatar", methods: ['POST'])]

    public function uploadAvatar(Request $request): Response
    {

        $file = $request->files->get('avatar');

        if ($file) {

            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            $directory = $this->getParameter('kernel.project_dir') . '/public/uploads/avatars';

            $file->move($directory, $fileName);

            $user = $this->getUser();
            $user->setAvatar('uploads/avatars/' . $fileName);

            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_profil');
        }
    }
    #[Route('admin/profil/operation_profil', name: 'app_admin_operation_profil', methods: ['GET'])]
    public function index_profil(OperationRepository $operationRepository): Response
    {
        $currentUser = $this->getUser();

        if ($currentUser) {

            $operations = $operationRepository->findBy(['user' => $currentUser]);
        } else {

            $operations = [];
        }

        return $this->render('admin/profil/operation_profil.html.twig', [
            'operations' => $operations,
        ]);
    }

    #[Route('/admin/profil/operation_termine/{userId}', name: 'app_operation_termine', methods: ['GET'])]
    public function operationTermine($userId, UserRepository $userRepository, OperationRepository $operationRepository): Response
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $user->setOperationsFinalisee($user->getOperationsFinalisee() + 1);

        $user->setOperationEnCours($user->getOperationEnCours() - 1);

        $operation = $operationRepository->findOneBy(['user' => $user, 'status_operation' => false]);

        if (!$operation) {
            throw $this->createNotFoundException("Aucune opération en cours pour cet utilisateur");
        }

        $operation->setStatusOperation(true);
        $operation->setDateFin(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_operation_profil');
    }
}
