<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
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
              SUM(payment.amount) AS payment_amount
            FROM purpose
              LEFT JOIN payment ON purpose.id = payment.purpose_id
              LEFT JOIN area ON payment.area_id = area.id
            WHERE purpose.archived_at IS NULL
            GROUP BY purpose.id, area.id
            ORDER BY ABS(area.number)
SQL
        );

        /** @var Area[] $areas */
        $areas = $em->createQueryBuilder()
            ->select('GROUP_CONCAT(user.realname) as owners')
            ->addSelect('area.number')
            ->from(Area::class, 'area', 'area.number')
            ->leftJoin(User::class, 'user', Join::WITH, 'user MEMBER OF area.users')
            ->groupBy('area.id')
            ->getQuery()
            ->getArrayResult();

        $purposes = [];
        $rows = array_map(function ($row) use (&$purposes, $areas) {
            $purposes[$row['purpose_id']] = $row['purpose_name'];

            return [
                'purpose' => [
                    'id' => $row['purpose_id'],
                    'name' => $row['purpose_name'],
                ],
                'area' => [
                    'number' => $row['area_number'],
                    'owners' => $areas[$row['area_number']]['owners'],
                ],
                'amount' => 0 > $row['payment_amount'] ? $row['payment_amount'] : 0,
            ];
        }, $data);

        return $this->render('arrears/index.html.twig', [
            'purposes' => $purposes,
            'rows' => $rows,
        ]);
    }
}
