<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait UpdatedAt
{
    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    protected function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
