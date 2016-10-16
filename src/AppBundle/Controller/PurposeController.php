<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Purpose;
use AppBundle\Entity\Repository\AreaRepository;
use AppBundle\Entity\Repository\PurposeRepository;
use AppBundle\EventDispatcher\Payment\PurposeEvent;
use AppBundle\Form\Model\PurposeModel;
use AppBundle\Form\Type\PurposeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Uuid\Uuid;

/**
 * @Route("/payment/type", service="app.controller.purpose")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PurposeController extends BaseController
{
    /**
     * @var PurposeRepository
     */
    private $purposeRepository;

    /**
     * @var AreaRepository
     */
    private $areaRepository;

    /**
     * @param PurposeRepository $purposeRepository
     * @param AreaRepository           $areaRepository
     */
    public function __construct(PurposeRepository $purposeRepository, AreaRepository $areaRepository)
    {
        $this->purposeRepository = $purposeRepository;
        $this->areaRepository = $areaRepository;
    }

    /**
     * @Route(name="purpose_list")
     */
    public function listAction(Request $request)
    {
        $pageSize = 20;
        $pageIndex = $request->query->get('page', 1);

        return $this->render(':purpose:list.html.twig', [
            'pagerfanta' => $this->purposeRepository->paginateLatest($pageSize, $pageIndex),
        ]);
    }

    /**
     * @Route("/new", name="purpose_new")
     *
     * @Security("is_granted(constant('AppRoles::CHAIRMAN'))")
     */
    public function newAction(Request $request)
    {
        $model = new PurposeModel();
        $form = $this->createForm(PurposeType::class, $model, [
            'action' => $this->generateUrl('purpose_new'),
            'areas' => $this->areaRepository->findAllForChoices('number'),
        ]);

        if ($form->handleRequest($request)->isValid()) {
            $entity = new Purpose(Uuid::create(), $model->name, $model->amount, $model->schedule, $model->calculation);

            $this->purposeRepository->save($entity);
            $this->dispatch(\AppEvents::PAYMENT_TYPE_NEW, new PurposeEvent($entity, $model, $this->getUser()));

            $this->success(sprintf('Платежная цель "%s" создана!', $model->name));

            return $this->redirectToRoute('purpose_list');
        }

        return $this->render(':purpose:edit.html.twig', [
           'form' => $form->createView(),
        ]);
    }
}
