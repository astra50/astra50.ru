<?php

namespace AppBundle;

use AppBundle\Uuid\UuidBuilder;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        $uuidFactory = new UuidFactory();
        $uuidBuilder = new UuidBuilder($uuidFactory->getNumberConverter());
        $uuidFactory->setUuidBuilder($uuidBuilder);
        $uuidFactory->setCodec(new TimestampFirstCombCodec($uuidBuilder));
        Uuid::setFactory($uuidFactory);
    }
}
