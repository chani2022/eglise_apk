<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Websocket\ArticleSocket;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class SocketCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private RequestStack $requestStack)
    {
        parent::__construct();
    }
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'ratchet:server';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Server socket run in 127.0.0.1:8080',
            '============',
            '',
        ]);

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ArticleSocket($this->em, $this->requestStack)
                )
            ),
            8080
        );

        $server->run();
        return Command::SUCCESS;
    }
}