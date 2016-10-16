<?php

namespace AppBundle\Form\Model;

use AppBundle\Entity\Area;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class AreaModel
{
    /**
     * @var int
     *
     * @Assert\NotBlank()
     */
    public $size;

    /**
     * @var UuidInterface
     *
     * @Assert\Type("Ramsey\Uuid\UuidInterface")
     */
    public $street;

    /**
     * @var UuidInterface[]
     *
     * @Assert\All({
     *      @Assert\Type("Ramsey\Uuid\UuidInterface"),
     * })
     */
    public $users = [];

    /**
     * @param Area $area
     *
     * @return AreaModel
     */
    public static function fromEntity(Area $area)
    {
        $model = new self();
        $model->size = $area->getSize();
        if ($street = $area->getStreet()) {
            $model->street = $street->getId();
        }
        foreach ($area->getUsers() as $user) {
            $model->users[] = $user->getId();
        }

        return $model;
    }
}
