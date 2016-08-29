<?php

namespace AppBundle\Controller;

use AppBundle\Form\AccountType;
use AppBundle\Model\AccountModel;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/account", service="app.controller.account")
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 *
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AccountController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(name="account")
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        $model = AccountModel::fromEntity($user);
        $form = $this->createForm(AccountType::class, $model, []);

        if ($form->handleRequest($request)->isValid()) {
            $user
                ->setRealname($model->realname)
                ->setPhone($model->phone)
                ->setPlacement($model->placement);

            $this->userRepository->save($user);

            $this->success('Изменения сохранены.');
        }

        return $this->render(':account:show.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}