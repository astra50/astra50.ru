<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/arrears")
 */
final class ArrearsController extends Controller
{
    /**
     * @Route(name="arrears")
     */
    public function indexAction(EntityManagerInterface $em): Response
    {
        $data = $em->getConnection()->fetchAll(<<<SQL
            SELECT
              purpose.id AS purpose_id,
              purpose.name AS purpose_name,
              area.number AS area_number,
              GROUP_CONCAT(DISTINCT u.realname) AS owners,
              SUM(payment.amount) AS payment_amount
            FROM purpose
              LEFT JOIN payment ON purpose.id = payment.purpose_id
              LEFT JOIN area ON payment.area_id = area.id
              LEFT JOIN area_user a ON area.id = a.area_id
              LEFT JOIN users u ON a.user_id = u.id
            WHERE purpose.archived_at IS NULL
            GROUP BY purpose.id, area.id
            ORDER BY ABS(area.number)
SQL
        );

        $purposes = [];
        $rows = array_map(function ($row) use (&$purposes) {
            $purposes[$row['purpose_id']] = $row['purpose_name'];

            return [
                'purpose' => [
                    'id' => $row['purpose_id'],
                    'name' => $row['purpose_name'],
                ],
                'area' => [
                    'number' => $row['area_number'],
                    'owners' => explode(',', $row['owners']),
                ],
                'amount' => $row['payment_amount'],
            ];
        }, $data);

        return $this->render('arrears/index.html.twig', [
            'purposes' => $purposes,
            'rows' => $rows,
        ]);
    }
}
