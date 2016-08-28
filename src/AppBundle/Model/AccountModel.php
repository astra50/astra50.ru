<?php

namespace AppBundle\Model;

use AppBundle\Entity\User;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AccountModel
{
    /**
     * @var string
     */
    public $realname;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $placement;

    /**
     * @param User $user
     *
     * @return static
     */
    public static function fromEntity(User $user)
    {
        $model = new static();
        $model->realname = $user->getRealname();
        $model->phone = $user->getPhone();
        $model->placement = $user->getPlacement();

        return $model;
    }
}
