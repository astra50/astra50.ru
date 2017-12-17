<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use App\Entity\Enum\ReportType;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReportTypeType extends EnumType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'report_type_enum';
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(): string
    {
        return ReportType::class;
    }
}
