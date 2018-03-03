<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('ru');

        $user = new User();
        $user->setUsername('preemiere@ya.ru');
        $user->setRealname('Константин');
        $user->addRole('ROLE_ADMIN');
        $user->changePassword('1234', $this->encoderFactory->getEncoder($user));

        $this->addReference('user-1', $user);
        $manager->persist($user);

        $user = new User();
        $user->setUsername($faker->email);
        $user->setRealname($faker->name);
        $user->changePassword($faker->password, $this->encoderFactory->getEncoder($user));

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
