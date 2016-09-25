<?php

namespace AppBundle\EventDispatcher\Payment;

use AppBundle\Entity\Payment;
use AppBundle\Entity\PaymentType;
use AppBundle\Entity\Repository\AreaRepository;
use AppBundle\Entity\Repository\PaymentRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Uuid\Uuid;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class NewPaymentTypeListener implements EventSubscriberInterface
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
            \AppEvents::PAYMENT_TYPE_NEW => 'onNewPaymentType',
        ];
    }

    /**
     * @param NewPaymentTypeEvent $event
     */
    public function onNewPaymentType(NewPaymentTypeEvent $event)
    {
        $paymentType = $event->getPaymentType();
        $model = $event->getPaymentTypeModel();
        $user = $event->getUser();

        $sum = null;
        if ($model->calculation === PaymentType::CALCULATION_SHARE) {
            $sum = (int) ceil($model->sum / count($model->areas));
        }

        foreach ($model->areas as $id) {
            $area = $this->areaRepository->get(Uuid::fromString($id));

            if (!$sum) {
                if (PaymentType::CALCULATION_SIZE === $model->calculation) {
                    $sum = $area->getSize() * $model->sum;
                } elseif (PaymentType::CALCULATION_EACH === $model->calculation) {
                    $sum = $model->sum;
                } else {
                    throw new \DomainException();
                }
            }

            if (0 < $sum) {
                $sum *= -1;
            }

            $this->paymentRepository->save(new Payment(Uuid::create(), $area, $paymentType, $user, $sum));
        }
    }
}
