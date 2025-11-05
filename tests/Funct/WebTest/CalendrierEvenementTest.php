<?php

namespace App\Tests\Funct\WebTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;

class CalendrierEvenementTest extends WebTestCase
{
    /** @var KernelBrowser $client */
    private $client;

    public function setUp(): void
    {
        $this->client = self::createClient();
    }
    public function testPageCalendrierEvenementFound(): void
    {
        /** @var Crawler $crawler */
        $crawler = $this->client->request("GET", '/');

        $this->assertResponseIsSuccessful();

        // $link = $crawler->selectLink('Voir tous les évènements')->link();
        $this->assertSelectorExists(".view-all-event");
        // dump($link);
        // $this->client->clickLink("Voir tous les évènements");

        // $this->assertResponseIsSuccessful();
    }
}
