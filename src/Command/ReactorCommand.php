<?php

declare(strict_types=1);

namespace App\Command;

use App\Kernel;
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
use Symfony\Component\HttpKernel\TerminableInterface;

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
     * @var Kernel
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

                $hash = hash_file('md5', $path);

                $tags = $request->getHeader('If-None-Match');
                if ($tags && in_array($hash, $tags, true)) {
                    return new Response(304);
                }

                return new Response(200, ['ETag' => $hash], fopen($path, 'rb'));
            },
            function (ServerRequestInterface $serverRequest) use ($loop) {
                $request = $this->httpFoundationFactory->createRequest($serverRequest);
                $response = $this->kernel->handle($request);
                $serverResponse = $this->psr7Factory->createResponse($response);

                if ($this->kernel instanceof TerminableInterface) {
                    $loop->addTimer(1, function () use ($request, $response) {
                        $this->kernel->terminate($request, $response);
                    });
                }

                return $serverResponse;
            },
        ]));

        $socket = new \React\Socket\Server('0.0.0.0:80', $loop);
        $server->listen($socket);

        $this->io->note('Listening on: '.$socket->getAddress());

        $loop->run();
    }
}
