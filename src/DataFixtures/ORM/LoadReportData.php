<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\Enum\Financing;
use App\Entity\Enum\ReportType;
use App\Entity\Report;
use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Generator;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class LoadReportData extends AbstractFixture implements OrderedFixtureInterface
{
    private const REPORTS_COUNT = 30;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user-1');

        $index = 0;
        foreach ($this->generateReports() as $report) {
            ++$index;

            $manager->persist($report);

            if ($index >= self::REPORTS_COUNT) {
                break;
            }
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }

    /**
     * @return Generator|Report[]
     */
    private function generateReports(): Generator
    {
        $faker = Factory::create('ru');

        $typeGenerator = $this->generateReportType();
        $financingGenerator = $this->generateFinancing();

        while (true) {
            $report = new Report();
            $report->setType($typeGenerator->current());
            $report->setFinancing($financingGenerator->current());
            $report->setName($faker->name);
            $report->setMonth($faker->randomElement(range(1, 12)));
            $report->setYear($faker->randomElement(Report::allowedYears()));
            $report->setUrl($faker->url);

            yield $report;

            $typeGenerator->next();
            $financingGenerator->next();
        }
    }

    /**
     * @return Generator|ReportType[]
     */
    private function generateReportType(): Generator
    {
        while (true) {
            foreach (ReportType::all() as $type) {
                yield $type;
            }
        }
    }

    private function generateFinancing(): Generator
    {
        while (true) {
            foreach (Financing::all() as $type) {
                yield $type;
            }
        }
    }
}
