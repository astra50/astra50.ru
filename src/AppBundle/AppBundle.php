<?php

namespace AppBundle;

use AppBundle\Doctrine\ClassMetadataUuid;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        $this->container->get('doctrine.orm.entity_manager')->getMetadataFactory()->setMetadataFor(Uuid::class, new ClassMetadataUuid(Uuid::class));
    }
}
