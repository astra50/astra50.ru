<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\News;
use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class LoadNewsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user-1');

        foreach ($this->generateNews() as [$title, $text, $internal, $published]) {
            $news = new News($user, $title, $text, $internal);

            if ($published) {
                $news->publish();
            }

            $manager->persist($news);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }

    /**
     * @return \Generator|News[]
     */
    private function generateNews(): \Generator
    {
        $faker = Factory::create('ru');

        for ($i = 0; $i < 4; ++$i) {
            yield [$faker->title, $faker->text, $i >= 2, true];
        }
    }
}
