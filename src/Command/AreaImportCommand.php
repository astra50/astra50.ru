<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Area;
use App\Repository\AreaRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AreaImportCommand extends Command
{
    /**
     * @var AreaRepository
     */
    private $areaRepository;

    public function __construct(AreaRepository $areaRepository)
    {
        parent::__construct();

        $this->areaRepository = $areaRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:area:import')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to json file');
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $path = $input->getArgument('path');

        if (!file_exists($path) && !is_readable($path)) {
            throw new InvalidArgumentException(sprintf('File "%s" not found or not readable', $path));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $array = (array) json_decode(file_get_contents($input->getArgument('path')), false);

        $io->progressStart(count($array));
        foreach ($array as [$number, $size]) {
            $this->areaRepository->save(new Area($number, $size));
            $io->progressAdvance();
        }
        $io->progressFinish();
    }
}
