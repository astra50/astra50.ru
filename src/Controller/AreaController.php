<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Area;
use App\Entity\Payment;
use App\Entity\Purpose;
use App\Entity\Street;
use App\Entity\User;
use App\Form\Model\AreaModel;
use App\Form\Type\AreaType;
use App\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/area")
 */
final class AreaController extends Controller
{
    public const PAYMENT_STATUS_SUCCESS = 1;
    public const PAYMENT_STATUS_FAILURE = 2;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route(name="area_index")
     *
     * @Security("is_granted(constant('App\\Roles::CHAIRMAN'))")
     */
    public function indexAction()
    {
        $areas = $this->em->createQueryBuilder()
            ->select('entity', 'u')
            ->from(Area::class, 'entity')
            ->leftJoin('entity.users', 'u')
            ->orderBy('entity.number + 0', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('/area/index.html.twig', [
            'areas' => $areas,
        ]);
    }

    /**
     * @Route("/{number}", name="area_show")
     * @Route("/{number}/{status}", name="area_show_payment_return", requirements={"status": "1|2"})
     *
     * @Security("is_granted(constant('App\\Roles::COMMUNITY'))")
     */
    public function showAction(Area $area, int $status = null)
    {
        $user = $this->getUser();

        if (!$area->isOwner($user) && !$this->isGranted(Roles::CHAIRMAN)) {
            throw new AccessDeniedException();
        }

        if (null !== $status) {
            switch ($status) {
                case self::PAYMENT_STATUS_SUCCESS:
                    $this->addFlash('success', 'Платёж успешно проведён платежной системой!');
                    break;
                case self::PAYMENT_STATUS_FAILURE:
                    $this->addFlash('error', 'В процессе оплаты произошла ошибка!');
                    break;
            }

            return $this->redirectToRoute('area_show', [
                'number' => $area->getNumber(),
            ]);
        }

        $items = $this->em->createQueryBuilder()
            ->select('purpose')
            ->addSelect('SUM(CASE WHEN payment.amount < 0 THEN payment.amount ELSE 0 END) AS bill')
            ->addSelect('SUM(CASE WHEN payment.amount > 0 THEN payment.amount ELSE 0 END) AS paid')
            ->from(Purpose::class, 'purpose')
            ->leftJoin(Payment::class, 'payment', Join::WITH, 'purpose = payment.purpose')
            ->where('payment.area = :area')
            ->andWhere('purpose.archivedAt IS NULL')
            ->setParameter('area', $area)
            ->groupBy('purpose')
            ->orderBy('purpose.id', 'DESC')
            ->getQuery()
            ->getArrayResult();

        $balance = $this->em->createQueryBuilder()
            ->select('SUM(p.amount)')
            ->from(Payment::class, 'p')
            ->join('p.purpose', 'purpose')
            ->where('p.area = :area')
            ->andWhere('purpose.archivedAt IS NULL')
            ->setParameter('area', $area)
            ->getQuery()
            ->getSingleScalarResult();

        /** @var Payment[] $data */
        $data = $this->em->createQueryBuilder()
            ->select('payment')
            ->from(Payment::class, 'payment')
            ->where('payment.area = :area')
            ->andWhere('payment.purpose IN (:purposes)')
            ->orderBy('payment.createdAt', 'DESC')
            ->setParameters([
                'area' => $area,
                'purposes' => array_map(function (array $item) {
                    return $item[0]['id'];
                }, $items),
            ])
            ->getQuery()
            ->getResult();

        $payments = [];
        foreach ($data as $payment) {
            $payments[$payment->getPurpose()->getId()][] = $payment;
        }

        return $this->render('area/show.html.twig', [
            'area' => $area,
            'items' => $items,
            'balance' => $balance,
            'payments' => $payments,
        ]);
    }

    /**
     * @Route("/{number}/edit", name="area_edit")
     *
     * @Security("is_granted(constant('App\\Roles::CHAIRMAN'))")
     */
    public function editAction(Request $request, Area $area)
    {
        $model = AreaModel::fromEntity($area);

        $form = $this->createForm(AreaType::class, $model, [
            'streets' => $this->em->getRepository(Street::class)->findAll(),
            'users' => $this->em->getRepository(User::class)->findAll(),
        ])
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $area->setSize($model->size);
            if ($model->street instanceof Street) {
                $area->setStreet($model->street);
            }

            $area->replaceUsers($model->users);

            foreach ($model->users as $user) {
                $user->addRole(Roles::COMMUNITY);
            }

            $this->em->flush();

            return $this->redirectToRoute('area_index');
        }

        return $this->render('area/edit.html.twig', [
            'area' => $area,
            'form' => $form->createView(),
        ]);
    }
}
