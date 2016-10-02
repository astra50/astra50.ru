<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\PaymentPurpose;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class PaymentPurposeExtension extends \Twig_Extension
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
            PaymentPurpose::SCHEDULE_ONCE => 'Разовая',
            PaymentPurpose::SCHEDULE_MONTHLY => 'Ежемесячная',
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
            PaymentPurpose::CALCULATION_EACH => 'За участок',
            PaymentPurpose::CALCULATION_SIZE => 'За сотку',
            PaymentPurpose::CALCULATION_SHARE => 'Поровну',
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
