<?php

namespace App\Tests\Funct\WebTest;

use App\Entity\User;
use App\Service\RecaptchaService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class LoginTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    /** @var  KernelBrowser $client*/
    private $client;

    private array $user;

    private Container $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->container = static::getContainer();
        $dir_fixtures = $this->container->getParameter('fixtures');
        $files = [
            $dir_fixtures . '/User.yaml',
        ];

        $loader = $this->container->get('fidry_alice_data_fixtures.loader.doctrine');
        $this->user = $loader->load($files);
    }

    public function testLoginSuccessWithEmailAndPassword(): void
    {


        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('#signIn', 'Se connecter');

        $link = $crawler->selectLink('Se connecter')->link();

        $crawler = $this->client->click($link);

        $this->assertSelectorExists("#form_signIn");

        // $recaptcha = new RecaptchaService($this->container->getParameter('google_recaptcha_site_key'));
        // $recaptcha->verify(Request::create("/", "GET"))
        $redac = $this->user['redacteur'];

        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues([
            "email" => $redac->getEmail(),
            "password" => $redac->getPassword(),
            "g-recaptcha-response" => true
        ]);

        $crawler = $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains("#signIn", $redac->getEmail());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
