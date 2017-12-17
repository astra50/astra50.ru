<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\Enum\Financing;
use App\Entity\Enum\ReportType;
use App\Entity\Report;
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
        foreach ($this->generateReports() as $index => $report) {
            $manager->persist($report);

            if ($index >= self::REPORTS_COUNT) {
                break;
            }
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
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

        $types = ReportType::all();
        $financings = Financing::all();
        $months = range(1, 12);
        $years = Report::allowedYears();

        while (true) {
            $report = new Report();
            $report->setType($faker->randomElement($types));
            $report->setFinancing($faker->randomElement($financings));
            $report->setName($faker->name);
            $report->setMonth($faker->randomElement($months));
            $report->setYear($faker->randomElement($years));
            $report->setUrl($faker->url);

            yield $report;
        }
    }
}
