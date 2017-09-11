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
        $faker = Factory::create('ru');

        /** @var User $user */
        $user = $this->getReference('user-1');

        $manager->persist(new News($user, $faker->title, $faker->text, true));
        $manager->persist(new News($user, $faker->title, $faker->text, false));

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
