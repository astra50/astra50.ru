<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

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
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
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
            ->addChild('Home', ['label' => 'Новости', 'route' => 'homepage'])->getParent()
            ->addChild('Finance', ['label' => 'Финансовая отчетность', 'route' => 'under_construction'])->getParent()
            ->addChild('Gallery', ['label' => 'Галерея', 'route' => 'under_construction'])->getParent()
            ->addChild('Suggestions', ['label' => 'Предложения', 'route' => 'under_construction'])->getParent()
            ->addChild('Payment', ['label' => 'Оплата', 'route' => 'under_construction'])->getParent()
            ->addChild('Contacts', ['label' => 'Контакты', 'route' => 'under_construction'])->getParent()
            ;

        return $menu;
    }
}
