<?php

namespace App\Tests\Funct\WebTest;

use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class HomeTest extends WebTestCase
{
    use ReloadDatabaseTrait;

    private KernelBrowser $client;
    protected Crawler $crawler;
    protected $object_fixtures;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->crawler = $this->client->request('GET', '/');
        $container = static::getContainer();
        $dir_fixtures = $container->getParameter('fixtures');
        $files = [
            $dir_fixtures . '/User.yaml',
            $dir_fixtures . '/Langue.yaml',
            $dir_fixtures . '/Categorie.yaml',
            $dir_fixtures . '/Article.yaml',
            $dir_fixtures . '/Comments.yaml',
        ];

        $loader = $container->get('fidry_alice_data_fixtures.loader.doctrine');
        $this->object_fixtures = $loader->load($files);
    }

    public function testHome(): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('a.test', 'Accueil');
    }

    public function testHasLinkToShowArticlePopulaire(): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(".article-populaires", "Voir tous les Actualité populaire");
    }

    public function testShowAllArticlePopulaire(): void
    {
        $this->assertResponseIsSuccessful();
        $link = $this->crawler->filter('.article-populaires')->link();

        $this->crawler = $this->client->click($link);

        $this->assertEquals(31, $this->crawler->filter('.article-Populaire')->count());

        // $this->assertEquals(3, count($article->getComments()));
    }
}
