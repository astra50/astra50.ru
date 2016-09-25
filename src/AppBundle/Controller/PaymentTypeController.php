<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\DoctrineUtils;
use AppBundle\Entity\PaymentType;
use AppBundle\EventDispatcher\Payment\NewPaymentTypeEvent;
use AppBundle\Form\Type\PaymentTypeType;
use AppBundle\Form\Model\PaymentTypeModel;
use AppBundle\Entity\Repository\AreaRepository;
use AppBundle\Entity\Repository\PaymentTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Uuid\Uuid;

/**
 * @Route("/payment/type", service="app.controller.payment_type")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PaymentTypeController extends Controller
{
    /**
     * @var PaymentTypeRepository
     */
    private $paymentTypeRepository;

    /**
     * @var AreaRepository
     */
    private $areaRepository;

    /**
     * @param PaymentTypeRepository $paymentTypeRepository
     */
    public function __construct(PaymentTypeRepository $paymentTypeRepository, AreaRepository $areaRepository)
    {
        $this->paymentTypeRepository = $paymentTypeRepository;
        $this->areaRepository = $areaRepository;
    }

    /**
     * @Route(name="payment_type")
     */
    public function listAction(Request $request)
    {
        $pageSize = 20;
        $pageIndex = $request->query->get('page', 1);

        return $this->render(':payment_type:list.html.twig', [
            'pagerfanta' => $this->paymentTypeRepository->paginateLatest($pageSize, $pageIndex),
        ]);
    }

    /**
     * @Route("/new", name="payment_type_new")
     *
     * @Security("is_granted(constant('AppRoles::CHAIRMAN'))")
     */
    public function newAction(Request $request)
    {
        $model = new PaymentTypeModel();
        $form = $this->createForm(PaymentTypeType::class, $model, [
            'action' => $this->generateUrl('payment_type_new'),
            'areas' => DoctrineUtils::arrayToChoices($this->areaRepository->findAllForChoices(), 'number'),
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $entity = new PaymentType(Uuid::create(), $model->name, $model->sum, $model->schedule, $model->calculation);

            $this->paymentTypeRepository->save($entity);
            $this->dispatch(\AppEvents::PAYMENT_TYPE_NEW, new NewPaymentTypeEvent($entity, $model, $this->getUser()));

            $this->success(sprintf('Платеж "%s" создан!', $model->name));

            return $this->redirectToRoute('payment_type');
        }

        return $this->render(':payment_type:edit.html.twig', [
           'form' => $form->createView(),
        ]);
    }
}
