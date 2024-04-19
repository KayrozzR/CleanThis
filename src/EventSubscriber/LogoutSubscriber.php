<?php 

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use App\Service\PostLogsService;
use DateTimeImmutable;

class LogoutSubscriber implements EventSubscriberInterface
{
    private $postLogsService;

    public function __construct(PostLogsService $postLogsService)
    {
        $this->postLogsService = $postLogsService;
    }

    public function onLogoutEvent(LogoutEvent $event)
    {
        // Récupérer l'email de l'utilisateur connecté avant la déconnexion
        $user = $event->getToken()->getUser();
        $userEmail = $user ? $user->getEmail() : null;

        if ($userEmail) {
            $now = new DateTimeImmutable();
            $tt = $now->format('Y-m-d H:i:s');

            $logData = [
                'EventTime' => $tt,
                'LoggerName' => 'Logout',
                'User' => $userEmail,
                'Message' => 'User logout in',
                'Level' => 'INFO',
                'Data' => 'User has been disconnected',
            ];

            try {
                $this->postLogsService->postLogs($logData);
            } catch (\Exception $e) {
                // Gérer les erreurs si la requête échoue
                echo ("Erreur");
            };
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}