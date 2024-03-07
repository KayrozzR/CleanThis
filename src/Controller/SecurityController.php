<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    public const SCOPES = [
        'google' => [],
    ];

    #[Route(path: '/login', name: 'auth_oauth_login', methods: ['GET' , 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // $user = new User();
        // $user->setEmail('johndoe@gmail.fr');
        // $user->setPassword($passwordHasher->hashPassword($user, '0000'));
        // $user->setRoles(['ROLE_USER']);
        // $em->persist($user);
        // $em->flush();

        if ($this->getUser()) {
            return $this->redirectToRoute('app_admin_profil');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'auth_oauth_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/oauth/connect/{service}', name: 'auth_oauth_connect', methods: ['GET'])]
    public function connect(string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
        if (!in_array($service, array_keys(self::SCOPES), true)) {
            throw $this->createNotFoundException();
        }

        return $clientRegistry->getClient($service)->redirect(self::SCOPES[$service]);
    }

    public function check(): Response
    {
        return new Response(status: 200);
    }


}

