<?php

namespace AppBundle\EventDispatcher\Payment;

use AppBundle\Entity\Payment;
use AppBundle\Entity\PaymentPurpose;
use AppBundle\Entity\Repository\AreaRepository;
use AppBundle\Entity\Repository\PaymentRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Uuid\Uuid;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NewPaymentPurposeListener implements EventSubscriberInterface
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
            \AppEvents::PAYMENT_TYPE_NEW => 'onNewPaymentPurpose',
        ];
    }

    /**
     * @param PaymentPurposeEvent $event
     */
    public function onNewPaymentPurpose(PaymentPurposeEvent $event)
    {
        $paymentPurpose = $event->getPaymentPurpose();
        $model = $event->getPaymentPurposeModel();
        $user = $event->getUser();

        $sum = null;
        if ($model->calculation === PaymentPurpose::CALCULATION_SHARE) {
            $sum = (int) (ceil(($model->sum / 100) / count($model->areas)) * 100);
        }

        foreach ($model->areas as $id) {
            $area = $this->areaRepository->get(Uuid::fromString($id));

            if (!$sum) {
                if (PaymentPurpose::CALCULATION_SIZE === $model->calculation) {
                    $sum = $area->getSize() * $model->sum;
                } elseif (PaymentPurpose::CALCULATION_EACH === $model->calculation) {
                    $sum = $model->sum;
                } else {
                    throw new \DomainException();
                }
            }

            if (0 < $sum) {
                $sum *= -1;
            }

            $this->paymentRepository->save(new Payment(Uuid::create(), $area, $paymentPurpose, $user, $sum));
        }
    }
}
