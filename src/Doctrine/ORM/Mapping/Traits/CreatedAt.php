<?php

declare(strict_types=1);

namespace App\Doctrine\ORM\Mapping\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait CreatedAt
{
    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
