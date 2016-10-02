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
        $menu = $this->factory->createItem('root', ['childrenAttributes' => ['class' => 'nav nav-justified']]);

        $menu
            ->addChild('News', ['label' => 'Новости', 'route' => 'news_list'])->getParent()
            ->addChild('Finance', ['label' => 'Финансовая отчетность', 'route' => 'under_construction'])->getParent()
            ->addChild('Gallery', ['label' => 'Галерея', 'route' => 'under_construction'])->getParent()
            ->addChild('Suggestions', ['label' => 'Предложения', 'route' => 'under_construction'])->getParent()
            ->addChild('Payment', ['label' => 'Оплата', 'route' => 'under_construction'])->getParent()
            ->addChild('Contacts', ['label' => 'Контакты', 'route' => 'contacts'])->getParent()
            ;

        return $menu;
    }

    public function createManagerMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root', ['childrenAttributes' => ['class' => 'nav navbar-nav']]);

        $menu
            ->addChild('Payment', ['label' => 'Платежи', 'route' => 'payment_list'])->getParent()
            ->addChild('Purpose', ['label' => 'Платежные цели', 'route' => 'purpose_list'])->getParent()
            ->addChild('Area', ['label' => 'Участки', 'route' => 'area_list'])->getParent()
            ;

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
