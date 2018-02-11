<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('ru');

        $user = new User('preemiere@ya.ru', $faker->name, $faker->password, true);
        $user->addRole('ROLE_ADMIN');
        $this->addReference('user-1', $user);
        $manager->persist($user);

        $user = new User($faker->email, $faker->name, $faker->password, true);
        $this->addReference('user-2', $user);

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(): int
    {
        return 1;
    }
}
