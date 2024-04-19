<?php

namespace App\Security;

use DateTimeImmutable;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use App\Service\PostLogsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    private PostLogsService $postLogsService;
    private LoggerInterface $logger;
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'auth_oauth_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, LoggerInterface $logger, PostLogsService $postLogsService)
    {
        $this->logger = $logger;
        $this->postLogsService = $postLogsService;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        $now= new DateTimeImmutable();
        $tt = $now->format('Y-m-d H:i:s');
        $user=$token->getUser(); 

        $logData = [
            'EventTime' => $tt,
            'LoggerName' => 'cnxApp',
            'User' => $user->getEmail(), // Vous pouvez utiliser le nom d'utilisateur ou toute autre information pertinente
            'Message' => 'User logged in successfully',
            'Level' => 'INFO',
            'Data' => 'User logged in successfully',
        ];

        try {
            $this->postLogsService->postLogs($logData);
        } catch (\Exception $e) {
            // Gérer les erreurs si la requête échoue
            $this->logger->error('Failed to log user login: ' . $e->getMessage());
        };
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_operation_profil'));           
        }elseif (in_array('ROLE_SENIOR', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_operation_profil'));
        }elseif (in_array('ROLE_APPRENTI', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin_operation_profil'));
        }else {
            return new RedirectResponse($this->urlGenerator->generate('app_user_profil'));
        }
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}