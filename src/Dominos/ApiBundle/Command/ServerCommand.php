<?php
namespace Dominos\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use React\EventLoop\Factory;

class ServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('push:server')
            ->setDescription('Start the Push server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $loop   = \React\EventLoop\Factory::create();
        $pusher = $this->getContainer()->get('pusher');

         // Listen for the web server to make a ZeroMQ push after an ajax request
        $context = new \React\ZMQ\Context($loop);
        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);

        $pull->bind('tcp://0.0.0.0:5557'); // Binding to 127.0.0.1 means the only client that can connect is itself

        $pull->on('message', array($pusher, 'onGetData'));

        // Set up our WebSocket server for clients wanting real-time updates
        $webSock = new \React\Socket\Server($loop);
        $webSock->listen(8080, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
        $webServer = new \Ratchet\Server\IoServer(
            new \Ratchet\Http\HttpServer(
                new \Ratchet\WebSocket\WsServer(
                    new \Ratchet\Wamp\WampServer(
                        $pusher
                    )
                )
            ),
            $webSock
        );

        $loop->run();


    }
}