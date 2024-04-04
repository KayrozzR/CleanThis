<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormTypeEmployee;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/login', name: 'auth_oauth_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($user = $this->getUser()) {
            if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SENIOR', $user->getRoles()) || in_array('ROLE_APPRENTI', $user->getRoles())) {
                return $this->redirectToRoute('app_admin_profil');
            } elseif (in_array('ROLE_CLIENT', $user->getRoles()))  {  
                return $this->redirectToRoute('app_user_profil'); 
            }
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'auth_oauth_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/oauth/connect/{service}', name: 'auth_oauth_connect', methods: ['GET'])]
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
        $scopes = [
            'google' => [],
        ];

        if (!in_array($service, array_keys($scopes), true)) {
            throw $this->createNotFoundException();
        }

        return $clientRegistry->getClient($service)->redirect($scopes[$service]);
    }

    #[Route(path: '/check', name: 'app_check')]
    public function check(): Response
    {
        return new Response(status: 200);
    }

    #[Route('/oubli-pass', name: 'forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        SendMailService $mail
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Trouver l'utilisateur par son email
            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            if ($user) {
                // Générer un token de réinitialisation 
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Générer un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // Créer les données de mail
                $context = compact('url', 'user');

                // Envoyer le mail
                $mail->send(
                    'user@example.com', // Mettez votre email ici
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset', // Chemin vers votre template de mail
                    $context
                );
                

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('auth_oauth_login');
            }

            // Utilisateur non trouvé
            $this->addFlash('danger', 'Aucun utilisateur trouvé avec cet email.');
            return $this->redirectToRoute('auth_oauth_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/oubli-pass/{token}', name: 'reset_pass')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasherInterface
     ): Response
    {
        $user = $userRepository->findOneByResetToken($token);
        if($user){
            $form = $this->createForm(ResetPasswordFormType::class);

        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $password = $form->get('password')->getData();
                $password2 = $form->get('password2')->getData();

                if ($password === $password2) {
                    //On efface le token
                    $user->setResetToken('');
                    $user->setPassword(
                        $userPasswordHasherInterface->hashPassword(
                            $user,
                            $password
                        )
                    );
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Mot de passe changé avec succès');
                    return $this->redirectToRoute('auth_oauth_login');
                } else {
                    $this->addFlash('warning', 'Les mots de passe ne correspondent pas.');
                }
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'Lien de réinitialisation invalide.');
        return $this->redirectToRoute('auth_oauth_login');
    }


    #[Route('/oubli-pass_employee/{token}', name: 'reset_pass_employee')]
    public function resetPassEmployee(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasherInterface
    ): Response
    {
        $user = $userRepository->findOneByResetToken($token);

        if($user){
            $form = $this->createForm(ResetPasswordFormTypeEmployee::class);
            $form->handleRequest($request);
            $correspondance = $user->getPassword();

            if ($form->isSubmitted() && $form->isValid()) {
                $passwordOld = $form->get('passwordReset')->getData();
                $password = $form->get('password')->getData();
                $password2 = $form->get('password2')->getData();
                $verif= password_verify($passwordOld,$correspondance);

                if ($verif &&($password === $password2)) {
                    //On efface le token
                    $user->setResetToken('');
                    $user->setPassword(
                        $userPasswordHasherInterface->hashPassword(
                            $user,
                            $password
                        )
                    );
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Mot de passe changé avec succès');
                    return $this->redirectToRoute('auth_oauth_login');
                } else {
                    $this->addFlash('warning', 'Les mots de passe ne correspondent pas.');
                }
            }

            return $this->render('security/reset_password_employee.html.twig', [
                'passForm' => $form->createView()
            ]);


        }
        // $this->addFlash('danger', 'mo');
        $this->addFlash('danger', 'Lien de réinitialisation invalide.');
        return $this->redirectToRoute('auth_oauth_login');
    }
}