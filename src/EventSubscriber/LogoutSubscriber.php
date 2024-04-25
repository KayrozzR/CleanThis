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
    $user = $event->getToken()->getUser();
    $userEmail = $user ? $user->getEmail() : null;

    
        $logoutTime = new DateTimeImmutable();


        $logData = [
            'EventTime' => $logoutTime->format('Y-m-d H:i:s'),
            'LoggerName' => 'Logout',
            'User' => $userEmail,
            'Message' => 'User logged out',
            'Level' => 'INFO',
            'Data' => '',
        ];

        try {
            $this->postLogsService->postLogs($logData);
        } catch (\Exception $e) {
            echo "Erreur lors de l'enregistrement du log : " . $e->getMessage();
        }
  
}

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}