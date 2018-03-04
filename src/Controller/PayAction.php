<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\Payment;
use App\Entity\Purpose;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/pay-url", name="pay_url")
 */
final class PayAction
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $secret;

    public function __construct(EntityManagerInterface $em, string $secret)
    {
        $this->em = $em;
        $this->secret = $secret;
    }

    public function __invoke(Request $request)
    {
        $em = $this->em;
        $secret = $this->secret;

        [
            'MNT_ID' => $id,
            'MNT_TRANSACTION_ID' => $transactionId,
            'MNT_OPERATION_ID' => $operationId,
            'MNT_AMOUNT' => $amount,
            'MNT_CURRENCY_CODE' => $currencyCode,
            'MNT_TEST_MODE' => $testMode,
            'MNT_SIGNATURE' => $signature,
            'MNT_PURPOSE' => $purposeId,
            'MNT_AREA' => $areaId,
            'MNT_USER' => $userId,
        ] = $request->request->all();

        $subscriberId = $request->request->get('MNT_SUBSCRIBER_ID', '');

        $verification = md5($id.$transactionId.$operationId.$amount.$currencyCode.$subscriberId.$testMode.$secret);

        if ($signature === $verification) {
            $user = $em->getRepository(User::class)->find($userId);
            $area = $em->getRepository(Area::class)->find($areaId);
            $purpose = $em->getRepository(Purpose::class)->find($purposeId);

            $em->persist(new Payment($area, $purpose, $user, (int) ($amount * 100), '# Оплата через moneta.ru'));
            $em->flush();

            return new Response('SUCCESS');
        }

        return new Response('FAILURE');
    }
}
