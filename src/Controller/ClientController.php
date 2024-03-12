<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\JWTService;
use Doctrine\ORM\EntityManager;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Form\CreatePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientController extends AbstractController
{
#[Route('/createPassword/{token}', name: 'create_password')]
public function createPassword(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasherInterface): Response
{

    // Création du formulaire
    $form = $this->createForm(CreatePasswordFormType::class);
    $form->handleRequest($request);

    // Vérification de la soumission du formulaire et de sa validation
    if ($form->isSubmitted() && $form->isValid()) {
       
        $password = $form->get('password')->getData();
        $password2 = $form->get('password2')->getData();
        $email = $form->get('email')->getData();

        // Recherche de l'utilisateur par e-mail
        $user = $userRepository->findOneByEmail($email);
        // var_dump($request->query->get('token')) ;
        // var_dump($user);
        // Si l'utilisateur est trouvé et les mots de passe correspondent
        if ($user && $user->getMailToken() == $request->query->get('token') && $password === $password2) {
            // Hachage du mot de passe
            $hashedPassword = $userPasswordHasherInterface->hashPassword(
                $user,
                $password
            );

            // Mise à jour du mot de passe de l'utilisateur
            $user->setPassword($hashedPassword);
            $entityManager->flush();

            // Redirection vers la page de connexion avec un message flash
            $this->addFlash('success', 'Votre mot de passe a bien été créé');
            return $this->redirectToRoute('auth_oauth_login');
        } else {
            // Ajouter un message d'erreur en cas de problème
            $this->addFlash('error', 'Les mots de passe ne correspondent pas ou l\'utilisateur n\'existe pas.');
        }
    }

    // Affichage du formulaire pour créer le mot de passe
    return $this->render('security/create_password.html.twig', [
        'form' => $form->createView()
    ]);
}
}