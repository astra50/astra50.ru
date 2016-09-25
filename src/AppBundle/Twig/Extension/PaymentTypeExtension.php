<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\PaymentType;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class PaymentTypeExtension extends \Twig_Extension
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
            PaymentType::SCHEDULE_ONCE => 'Разовая',
            PaymentType::SCHEDULE_MONTHLY => 'Ежемесячная',
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
            PaymentType::CALCULATION_EACH => 'За участок',
            PaymentType::CALCULATION_SIZE => 'За сотку',
            PaymentType::CALCULATION_SHARE => 'Поровну',
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
