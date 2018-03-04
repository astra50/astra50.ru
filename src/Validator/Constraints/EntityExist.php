<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class EntityExist extends Constraint
{
    public $entity = '';
    public $field = '';
    public $message = 'Такой сущности не существует';
    public $service = EntityExistValidator::class;

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return $this->service;
    }
}
