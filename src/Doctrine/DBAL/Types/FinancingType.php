<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use App\Entity\Enum\Financing;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class FinancingType extends EnumType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'financing_enum';
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(): string
    {
        return Financing::class;
    }
}
