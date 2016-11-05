<?php

namespace AppBundle\EventDispatcher\Payment;

use AppBundle\Entity\Payment;
use AppBundle\Entity\Purpose;
use AppBundle\Entity\Repository\AreaRepository;
use AppBundle\Entity\Repository\PaymentRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Uuid\Uuid;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NewPurposeListener implements EventSubscriberInterface
{
    /**
     * @var AreaRepository
     */
    private $areaRepository;

    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    /**
     * @param AreaRepository $areaRepository
     */
    public function __construct(AreaRepository $areaRepository, PaymentRepository $paymentRepository)
    {
        $this->areaRepository = $areaRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            \AppEvents::PAYMENT_TYPE_NEW => 'onNewPurpose',
        ];
    }

    /**
     * @param PurposeEvent $event
     *
     * @throws \DomainException
     */
    public function onNewPurpose(PurposeEvent $event)
    {
        $purpose = $event->getPurpose();
        $model = $event->getPurposeModel();
        $user = $event->getUser();

        $amount = null;
        if ($model->calculation === Purpose::CALCULATION_SHARE) {
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

            $this->paymentRepository->save(new Payment(Uuid::create(), $area, $purpose, $user, $amount));
        }
    }
}
