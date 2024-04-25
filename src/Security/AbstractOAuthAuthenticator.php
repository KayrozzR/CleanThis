<?php

namespace App\Security;

use App\Entity\User;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use App\Service\PostLogsService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

abstract class AbstractOAuthAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;
    private PostLogsService $postLogsService;
    private LoggerInterface $logger;

    protected string $serviceName = '';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly RouterInterface $router,
        private readonly UserRepository $repository,
        private readonly OAuthRegistrationService $registrationService,
        private EntityManagerInterface $entityManager,
        LoggerInterface $logger, 
        PostLogsService $postLogsService
    ) {
        $this->logger = $logger;
        $this->postLogsService = $postLogsService;
    }

    public function supports(Request $request): ?bool
    {
        return 'auth_oauth_check' == $request->attributes->get('_route') &&
            $request->get('service') == $this->serviceName;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName); 
        if ($targetPath) {
            return new RedirectResponse($targetPath); 
        }

        $now = new DateTimeImmutable();
        $loginTime = $now->format('Y-m-d H:i:s');
        $user = $token->getUser(); 
    
        $logData = [
            'EventTime' => $loginTime,
            'LoggerName' => 'Login',
            'User' => $user->getEmail(),
            'Message' => 'User logged in successfully',
            'Level' => 'INFO',
            'Data' => "",
        ];

    try {
        $this->postLogsService->postLogs($logData);
    } catch (\Exception $e) {
        $this->logger->error('Failed to log user login: ' . $e->getMessage());
    };

        $roles = $token->getRoleNames();

        if (in_array('ROLE_SENIOR', $roles) || in_array('ROLE_APPRENTI', $roles) || in_array('ROLE_EXPERT', $roles) || in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->router->generate('app_admin_operation_profil'));
        } else {
            return new RedirectResponse($this->router->generate('app_user_profil'));
        }

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('auth_oauth_login'));
    }

    /** 
     * @param GoogleUser $resourceOwner
     */
     
    public function authenticate(Request $request): SelfValidatingPassport
    {
        $credentials = $this->fetchAccessToken($this->getClient());
        $resourceOwner = $this->getRessourceOwnerFromCredentials($credentials);
        $user = $this->getUserFromResourceOwner($resourceOwner, $this->repository);



            $email = $resourceOwner->getEmail();
            $existingUser = $this->repository->findOneBy(['email' => $email]);



         if ($existingUser) {

            // Associer les informations Google à l'utilisateur existant
            $existingUser->setGoogleId($resourceOwner->getId());
            $existingUser->setAvatar($resourceOwner->getAvatar());

            // Enregistrer les modifications dans la base de données
            $this->entityManager->persist($existingUser);
            $this->entityManager->flush();


            // Retourner le user existant
            $user = $existingUser;



        return new SelfValidatingPassport(
            userBadge: new UserBadge($user->getUserIdentifier(), fn () => $user),
            badges: [
                new RememberMeBadge()
            ]
        ); }
        else {
            throw new CustomUserMessageAuthenticationException('Aucun utilisateur associé à cet e-mail Google.');
        }
    }

    protected function getRessourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface
    {
        return $this->getClient()->fetchUserFromToken($credentials);
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient($this->serviceName);
    }

    abstract protected function getUserFromResourceOwner(
        ResourceOwnerInterface $resourceOwner,
        UserRepository $repositery
    ): ?User;
}