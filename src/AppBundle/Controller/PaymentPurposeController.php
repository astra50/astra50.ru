<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\DoctrineUtils;
use AppBundle\Entity\PaymentPurpose;
use AppBundle\EventDispatcher\Payment\PaymentPurposeEvent;
use AppBundle\Form\Type\PaymentPurposeType;
use AppBundle\Form\Model\PaymentPurposeModel;
use AppBundle\Entity\Repository\AreaRepository;
use AppBundle\Entity\Repository\PaymentPurposeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Uuid\Uuid;

/**
 * @Route("/payment/type", service="app.controller.payment_type")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentPurposeController extends BaseController
{
    /**
     * @var PaymentPurposeRepository
     */
    private $paymentPurposeRepository;

    /**
     * @var AreaRepository
     */
    private $areaRepository;

    /**
     * @param PaymentPurposeRepository $paymentPurposeRepository
     */
    public function __construct(PaymentPurposeRepository $paymentPurposeRepository, AreaRepository $areaRepository)
    {
        $this->paymentPurposeRepository = $paymentPurposeRepository;
        $this->areaRepository = $areaRepository;
    }

    /**
     * @Route(name="payment_type_list")
     */
    public function listAction(Request $request)
    {
        $pageSize = 20;
        $pageIndex = $request->query->get('page', 1);

        return $this->render(':payment_type:list.html.twig', [
            'pagerfanta' => $this->paymentPurposeRepository->paginateLatest($pageSize, $pageIndex),
        ]);
    }

    /**
     * @Route("/new", name="payment_type_new")
     *
     * @Security("is_granted(constant('AppRoles::CHAIRMAN'))")
     */
    public function newAction(Request $request)
    {
        $model = new PaymentPurposeModel();
        $form = $this->createForm(PaymentPurposeType::class, $model, [
            'action' => $this->generateUrl('payment_type_new'),
            'areas' => DoctrineUtils::arrayToChoices($this->areaRepository->findAllForChoices(), 'number'),
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $entity = new PaymentPurpose(Uuid::create(), $model->name, $model->sum, $model->schedule, $model->calculation);

            $this->paymentPurposeRepository->save($entity);
            $this->dispatch(\AppEvents::PAYMENT_TYPE_NEW, new PaymentPurposeEvent($entity, $model, $this->getUser()));

            $this->success(sprintf('Платеж "%s" создан!', $model->name));

            return $this->redirectToRoute('payment_type_list');
        }

        return $this->render(':payment_type:edit.html.twig', [
           'form' => $form->createView(),
        ]);
    }
}
