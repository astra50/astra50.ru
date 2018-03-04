<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use App\Entity\Enum\Calculation;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CalculationType extends EnumType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'calculation_enum';
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(): string
    {
        return Calculation::class;
    }
}
