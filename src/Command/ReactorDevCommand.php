<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReactorDevCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->container = $container;
    }

    protected function configure(): void
    {
        $this->setName('reactor:dev');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $cwd = $this->container->getParameter('kernel.project_dir');

        $watch = (new ProcessBuilder())
            ->setPrefix('inotifywait')
            ->add('--monitor')
            ->add('--recursive')
            ->add('--format "%w %f"')
            ->add('--event close_write')
//            ->add('--event delete')
//            ->add('--event create')
            ->add('bin config public src templates vendor')
            ->setWorkingDirectory($cwd)
            ->setTimeout(0)
            ->getProcess();

        $watch->setCommandLine(str_replace('\'', '', $watch->getCommandLine()));

        $reactor = new Process('console reactor', $cwd, null, null, 0);
        $reactor->setPty(true);
        $callback = function ($type, $buffer) use ($output): void {
            dump(func_get_args());
            $output->write($buffer);
        };

        $reactor->start($callback);

        $watch->run(function ($type, $buffer) use ($reactor, $callback, $output): void {
            dump($reactor->getOutput());

            $output->writeln('Restart!');

            $reactor->stop();
            $reactor->restart();
        });
    }
}
