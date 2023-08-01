<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class LoginSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(private CacheInterface $cache)
    {
    }
    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        // $this->cache->get("user_auth_" . str_replace('@', '_', $event->getUser()->getUserIdentifier()), function (ItemInterface $item) use ($event) {
        //     return $event->getUser();
        // });
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
        ];
    }
}