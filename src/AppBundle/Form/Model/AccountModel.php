<?php

namespace AppBundle\Form\Model;

use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AccountModel
{
    /**
     * @var string
     *
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $realname;

    /**
     * @var string
     *
     * @Assert\NotBlank()
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
        $model->email = $user->getEmail();
        $model->realname = $user->getRealname();
        $model->phone = $user->getPhone();

        return $model;
    }
}
