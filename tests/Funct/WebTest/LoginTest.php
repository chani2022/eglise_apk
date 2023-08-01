<?php

namespace App\Tests\Funct\WebTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class LoginTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    /** @var  KernelBrowser $client*/
    private $client;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->user = $this->databaseTool->loadAliceFixture([
            dirname(__DIR__) . '/../../fixtures/User.yaml',
        ]);
        // or with Symfony < 5.3
        // static::bootKernel();
        // $this->databaseTool = self::$container->get(DatabaseToolCollection::class)->get();
    }

    public function testLoginSuccessWithEmailAndPassword(): void
    {
        // dd($this->user);
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#signIn', 'Se connecter');

        $link = $crawler->selectLink('Se connecter')->link();

        $this->client->click($link);

        $this->assertSelectorExists("#form_signIn");

        $form = $crawler->selectButton('Se connecter')->form();

        $this->client->submit($form);

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains("#signIn", $this->user['user']->getEmail());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}