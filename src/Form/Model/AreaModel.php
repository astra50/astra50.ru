<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Area;
use App\Entity\Street;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AreaModel
{
    /**
     * @var int
     *
     * @Assert\NotBlank
     */
    public $size;

    /**
     * @var Street|null
     *
     * @Assert\Type("App\Entity\Street")
     */
    public $street;

    /**
     * @var User[]
     *
     * @Assert\All({
     *     @Assert\Type("App\Entity\User"),
     * })
     */
    public $users = [];

    public static function fromEntity(Area $area): self
    {
        $model = new self();
        $model->size = $area->getSize();
        $model->street = $area->getStreet();
        $model->users = $area->getUsers();

        return $model;
    }
}
