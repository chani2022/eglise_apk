<?php

namespace App\EventSubscriber;

use App\Entity\Visitor;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class VisitorRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em, private CacheInterface $cache)
    {
    }

    public function onSaveUserAndToCache(RequestEvent $event): void
    {

        $url = $event->getRequest()->getPathInfo();
        $ip_client = $event->getRequest()->getClientIp();

        $user = $this->em->getRepository(Visitor::class)->findOneBy([
            "ip" => $ip_client,
            "visited_at" => new DateTimeImmutable()
        ]);


        if (!$user) {
            $visitor = new Visitor();
            $visitor->setIp($ip_client)
                ->setVisitedAt(new DateTimeImmutable())
                ->setUrl($url);

            try {
                $this->em->persist($visitor);
                $this->em->flush();
            } catch (Exception $e) {
                dd("error" . $e->getMessage());
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onSaveUserAndToCache', 4],
            ]
        ];
    }
}