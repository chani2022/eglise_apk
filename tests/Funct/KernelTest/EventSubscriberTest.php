<?php

namespace App\Tests\Funct\KernelTest;

use App\Entity\Visitor;
use App\EventSubscriber\VisitorRequestSubscriber;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class EventSubscriberTest extends KernelTestCase
{

    private $em;
    public function setUp(): void
    {
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->em->createQueryBuilder()
            ->delete(Visitor::class, 'v')
            ->getQuery()
            ->execute();
    }

    public function testVisitorRequest(): void
    {
        $this->assertContains('onKernelRequest', VisitorRequestSubscriber::getSubscribedEvents());
        $request = Request::create(
            '/',
            'GET',
        );

        $requestEvent = new RequestEvent(self::$kernel, $request, HttpKernelInterface::MAIN_REQUEST);
        $dispatcher = new EventDispatcher();
        $visitorSubscriber = new VisitorRequestSubscriber($this->em);

        $dispatcher->addSubscriber($visitorSubscriber);

        $dispatcher->dispatch($requestEvent, KernelEvents::REQUEST);

        $visitors = $this->em->getRepository(Visitor::class)->findBy(['visited_at' => new DateTimeImmutable()]);

        $this->assertEquals(1, count($visitors));
    }
}