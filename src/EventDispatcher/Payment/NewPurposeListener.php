<?php

declare(strict_types=1);

namespace App\EventDispatcher\Payment;

use App\Entity\Payment;
use App\Entity\Purpose;
use App\Repository\PaymentRepository;
use App\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NewPurposeListener implements EventSubscriberInterface
{
    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    /**
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::PAYMENT_TYPE_NEW => 'onNewPurpose',
        ];
    }

    /**
     * @param PurposeEvent $event
     *
     * @throws \DomainException
     */
    public function onNewPurpose(PurposeEvent $event): void
    {
        $purpose = $event->getPurpose();
        $model = $event->getPurposeModel();
        $user = $event->getUser();

        $amount = null;
        if (Purpose::CALCULATION_SHARE === $model->calculation) {
            $amount = (int) ceil($model->amount / count($model->areas));
        }

        foreach ($model->areas as $area) {
            if (!$amount) {
                if (Purpose::CALCULATION_SIZE === $model->calculation) {
                    $amount = $area->getSize() / 100 * $model->amount;
                } elseif (Purpose::CALCULATION_EACH === $model->calculation) {
                    $amount = $model->amount;
                } else {
                    throw new \DomainException();
                }
            }

            if (0 < $amount) {
                $amount *= -1;
            }

            $this->paymentRepository->save(new Payment($area, $purpose, $user, $amount));
        }
    }
}
