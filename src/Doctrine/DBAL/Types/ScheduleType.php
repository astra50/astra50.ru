<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use App\Entity\Enum\Schedule;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ScheduleType extends EnumType
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'schedule_enum';
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(): string
    {
        return Schedule::class;
    }
}
