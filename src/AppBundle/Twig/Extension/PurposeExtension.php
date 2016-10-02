<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\Purpose;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class PurposeExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('payment_schedule', [$this, 'paymentScheduleToString']),
            new \Twig_SimpleFilter('payment_calculation', [$this, 'paymentCalculationToString']),
        ];
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public function paymentScheduleToString(int $value): string
    {
        return [
            Purpose::SCHEDULE_ONCE => 'Разовая',
            Purpose::SCHEDULE_MONTHLY => 'Ежемесячная',
        ][$value];
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public function paymentCalculationToString(int $value): string
    {
        return [
            Purpose::CALCULATION_EACH => 'За участок',
            Purpose::CALCULATION_SIZE => 'За сотку',
            Purpose::CALCULATION_SHARE => 'Поровну',
        ][$value];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'payment_type';
    }
}
