<?php

namespace AppBundle\Command;

use AppBundle\Entity\Area;
use AppBundle\Entity\Repository\AreaRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Uuid\Uuid;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AreaImportCommand extends Command
{
    /**
     * @var AreaRepository
     */
    private $areaRepository;

    /**
     * @param AreaRepository $areaRepository
     */
    public function __construct(AreaRepository $areaRepository)
    {
        parent::__construct();

        $this->areaRepository = $areaRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:area:import')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to json file');
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        if (!file_exists($path) && !is_readable($path)) {
            throw new InvalidArgumentException(sprintf('File "%s" not found or not readable', $path));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $array = (array) json_decode(file_get_contents($input->getArgument('path')), false);

        $io->progressStart(count($array));
        foreach ($array as $item) {
            list ($number, $size) = $item;

            $this->areaRepository->save(new Area(Uuid::create(), $number, $size));
            $io->progressAdvance();
        }
        $io->progressFinish();
    }
}
