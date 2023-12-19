<?php

namespace App\Tests\Funct\WebTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class HomeTest extends WebTestCase
{
    private $client;
    protected Crawler $crawler;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->crawler = $this->client->request('GET', '/');
    }

    public function testHome(): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('a.test', 'Accueil');
    }

    public function testVoirTousArticle(): void
    {
        $this->assertResponseIsSuccessful();
    }
}
