<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use App\Service\JWTService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class TokenSubscriber implements EventSubscriberInterface
{
    private $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof TokenAuthenticatedController) {

            $token = $event->getRequest()->headers->get('Authorization');
            // проверка заголовок
            if ($token === null) {
                throw new UnauthorizedHttpException('', 'Заголовок Authorization не содержит JWT Bearer токен');
            }
            // проверка токена
            $tokenParts = explode(' ', $token);
            if (count($tokenParts) !== 2 || $tokenParts[0] !== 'Bearer') {
                throw new UnauthorizedHttpException('', 'Заголовок Authorization не содержит JWT Bearer токен');
            }
            // валидация токена
            $this->jwtService->validateToken($tokenParts[1]);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
