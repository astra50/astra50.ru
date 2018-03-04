<?php

declare(strict_types=1);

namespace App\Menu;

use App\Roles;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class Builder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createMainMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root', ['childrenAttributes' => ['class' => 'nav navbar-nav']]);

        $menu
            ->addChild('News', ['label' => 'Новости', 'route' => 'news_index'])->getParent()
            ->addChild('Finance', ['label' => 'Финансовая отчетность', 'route' => 'report_index'])->getParent()
//            ->addChild('Gallery', ['label' => 'Галерея', 'route' => 'gallery_index'])->getParent()
            ->addChild('Suggestions', ['label' => 'Предложения', 'route' => 'suggestions_new'])->getParent()
            ->addChild('Payment', ['label' => 'Оплата', 'route' => 'payment_index'])->getParent()
            ->addChild('Documents', ['label' => 'Документы', 'route' => 'documents_index'])->getParent()
            ->addChild('Contacts', ['label' => 'Контакты', 'route' => 'contacts'])->getParent();

        return $menu;
    }

    public function createManagerMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'dropdown-menu',
            ],
        ]);

        $menu
            ->addChild('Payment', ['label' => 'Платежи', 'route' => 'transaction_index'])
            ->setDisplay($this->authorizationChecker->isGranted(Roles::CASHIER))
            ->getParent()

            ->addChild('Purpose', ['label' => 'Платежные цели', 'route' => 'purpose_index'])
            ->setDisplay($this->authorizationChecker->isGranted(Roles::CHAIRMAN))
            ->getParent()

            ->addChild('Area', ['label' => 'Участки', 'route' => 'area_index'])
            ->setDisplay(
                $this->authorizationChecker->isGranted(Roles::CHAIRMAN)
                || $this->authorizationChecker->isGranted(Roles::CASHIER)
            )
            ->getParent()

            ->addChild('Street', ['label' => 'Улицы', 'route' => 'street_index'])
            ->setDisplay($this->authorizationChecker->isGranted(Roles::CHAIRMAN))
            ->getParent()

            ->addChild('Suggestions', ['label' => 'Предложения', 'route' => 'suggestions_index'])
            ->setDisplay($this->authorizationChecker->isGranted(Roles::CHAIRMAN))
            ->getParent()

            ->addChild('Arrears', ['label' => 'Задолженности', 'route' => 'arrears'])
            ->setDisplay(
                $this->authorizationChecker->isGranted(Roles::CHAIRMAN)
                || $this->authorizationChecker->isGranted(Roles::CASHIER)
            )
            ->getParent();

        return $menu;
    }
}
