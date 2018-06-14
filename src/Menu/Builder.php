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
        $menu = $this->factory->createItem('root', ['childrenAttributes' => ['class' => 'navbar-nav mr-auto']]);

        $attributes = ['class' => 'nav-item'];
        $linkAttributes = ['class' => 'nav-link'];

        $menu
            ->addChild('News', ['label' => 'Новости', 'route' => 'news_index'])
            ->setAttributes($attributes)
            ->setLinkAttributes($linkAttributes)
            ->getParent()

            ->addChild('Finance', ['label' => 'Финансовая отчетность', 'route' => 'report_index'])
            ->setAttributes($attributes)
            ->setLinkAttributes($linkAttributes)
            ->getParent()

            ->addChild('Suggestions', ['label' => 'Предложения', 'route' => 'suggestions_new'])
            ->setAttributes($attributes)
            ->setLinkAttributes($linkAttributes)
            ->getParent()

            ->addChild('Payment', ['label' => 'Оплата', 'route' => 'payment_index'])
            ->setAttributes($attributes)
            ->setLinkAttributes($linkAttributes)
            ->getParent()

            ->addChild('Documents', ['label' => 'Документы', 'route' => 'documents_index'])
            ->setAttributes($attributes)
            ->setLinkAttributes($linkAttributes)
            ->getParent()

            ->addChild('Contacts', ['label' => 'Контакты', 'route' => 'contacts'])
            ->setAttributes($attributes)
            ->setLinkAttributes($linkAttributes)
            ->getParent();

        return $menu;
    }

    public function createManagerMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => 'dropdown-menu',
            ],
        ]);

        $linkAttributes = ['class' => 'dropdown-item'];

        $menu
            ->addChild('Payment', ['label' => 'Платежи', 'route' => 'transaction_index'])
            ->setLinkAttributes($linkAttributes)
            ->setDisplay($this->authorizationChecker->isGranted(Roles::CASHIER))
            ->getParent()

            ->addChild('Purpose', ['label' => 'Платежные цели', 'route' => 'purpose_index'])
            ->setLinkAttributes($linkAttributes)
            ->setDisplay($this->authorizationChecker->isGranted(Roles::CHAIRMAN))
            ->getParent()

            ->addChild('Area', ['label' => 'Участки', 'route' => 'area_index'])
            ->setLinkAttributes($linkAttributes)
            ->setDisplay(
                $this->authorizationChecker->isGranted(Roles::CHAIRMAN)
                || $this->authorizationChecker->isGranted(Roles::CASHIER)
            )
            ->getParent()

            ->addChild('Street', ['label' => 'Улицы', 'route' => 'street_index'])
            ->setLinkAttributes($linkAttributes)
            ->setDisplay($this->authorizationChecker->isGranted(Roles::CHAIRMAN))
            ->getParent()

            ->addChild('Suggestions', ['label' => 'Предложения', 'route' => 'suggestions_index'])
            ->setLinkAttributes($linkAttributes)
            ->setDisplay($this->authorizationChecker->isGranted(Roles::CHAIRMAN))
            ->getParent()

            ->addChild('Arrears', ['label' => 'Задолженности', 'route' => 'arrears'])
            ->setLinkAttributes($linkAttributes)
            ->setDisplay(
                $this->authorizationChecker->isGranted(Roles::CHAIRMAN)
                || $this->authorizationChecker->isGranted(Roles::CASHIER)
            )
            ->getParent()

            ->addChild('Users', ['label' => 'Пользователи', 'route' => 'user_index'])
            ->setLinkAttributes($linkAttributes)
            ->setDisplay($this->authorizationChecker->isGranted(Roles::ADMIN))
            ->getParent();

        return $menu;
    }
}
