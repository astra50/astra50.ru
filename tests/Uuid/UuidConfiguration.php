<?php

namespace Tests;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Uuid\Uuid;

class UuidConfiguration extends \PHPUnit_Framework_TestCase
{
    public function testConfig()
    {
        self::assertInstanceOf(Uuid::class, RamseyUuid::uuid1());
    }
}
