<?php

declare(strict_types=1);

namespace App\Command;

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\MiddlewareRunner;
use React\Http\Response;
use React\Http\Server;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReactorCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var HttpFoundationFactory
     */
    private $httpFoundationFactory;

    /**
     * @var DiactorosFactory
     */
    private $psr7Factory;

    /**
     * @var string
     */
    private $webRoot;

    protected function configure(): void
    {
        $this->setName('reactor');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        /** @var Application $application */
        $application = $this->getApplication();
        $this->kernel = $application->getKernel();

        $this->httpFoundationFactory = new HttpFoundationFactory();
        $this->psr7Factory = new DiactorosFactory();

        $this->webRoot = $this->kernel->getContainer()->getParameter('kernel.root_dir');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $loop = Factory::create();

        $server = new Server(new MiddlewareRunner([
            function (ServerRequestInterface $request, callable $next) use ($loop) {
                $path = $this->webRoot.$request->getUri()->getPath();

                if (!is_file($path)) {
                    return $next($request);
                }

                return new Response(200, [], file_get_contents($path));
            },
            function (ServerRequestInterface $request) {
                $response = $this->kernel->handle($this->httpFoundationFactory->createRequest($request));

                return $this->psr7Factory->createResponse($response);
            },
        ]));

        $socket = new \React\Socket\Server('0.0.0.0:80', $loop);
        $server->listen($socket);

        $this->io->note('Listening on: '.$socket->getAddress());

        $loop->run();
    }
}
