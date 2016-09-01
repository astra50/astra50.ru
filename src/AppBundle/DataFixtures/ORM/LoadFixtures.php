<?php

namespace AppBundle\DataFixtures\ORM;

use Uuid\Uuid;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // fix   Notice: Use of undefined constant GLOB_BRACE - assumed 'GLOB_BRACE'
        define('GLOB_BRACE', 0);

        $objects = Fixtures::load(__DIR__.'/fixtures.yml', $manager, ['providers' => [$this]]);
    }

    public function uuid4()
    {
        return Uuid::create();
    }
}
