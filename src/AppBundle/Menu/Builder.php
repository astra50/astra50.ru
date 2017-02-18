<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

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
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, AuthorizationChecker $authorizationChecker)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function createMainMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root', ['childrenAttributes' => ['class' => 'nav navbar-nav']]);

        $menu
            ->addChild('News', ['label' => 'Новости', 'route' => 'news_index'])->getParent()
            ->addChild('Finance', ['label' => 'Финансовая отчетность', 'route' => 'report_index'])->getParent()
            ->addChild('Gallery', ['label' => 'Галерея', 'route' => 'gallery_index'])->getParent()
            ->addChild('Suggestions', ['label' => 'Предложения', 'route' => 'suggestions_new'])->getParent()
            ->addChild('Payment', ['label' => 'Оплата', 'route' => 'payment_index'])->getParent()
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
            ->addChild('Payment', ['label' => 'Платежи', 'route' => 'transaction_index'])->getParent()
            ->addChild('Purpose', ['label' => 'Платежные цели', 'route' => 'purpose_index'])->getParent()
            ->addChild('Area', ['label' => 'Участки', 'route' => 'area_index'])->getParent()
            ->addChild('Street', ['label' => 'Улицы', 'route' => 'street_index'])->getParent()
            ->addChild('Suggestions', ['label' => 'Предложения', 'route' => 'suggestions_index'])->getParent();

        return $menu;
    }

    /**
     * @param $role
     *
     * @return bool
     */
    private function isGranted($role)
    {
        return $this->authorizationChecker->isGranted($role);
    }
}
