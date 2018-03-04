<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class EntityExistValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EntityExist) {
            throw new UnexpectedTypeException($constraint, EntityExist::class);
        }

        if (null === $value || '' === $value) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $entity = $this->em->getRepository($constraint->entity)->findOneBy([$constraint->field => $value]);

        if (null === $entity) {
            $this->context->addViolation($constraint->message);
        }
    }
}
