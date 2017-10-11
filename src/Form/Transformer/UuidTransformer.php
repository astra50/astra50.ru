<?php

declare(strict_types=1);

namespace App\Form\Transformer;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UuidTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->doTransform($item);
            }

            return $value;
        }

        return $this->doTransform($value);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return $value;
        }

        try {
            if (is_array($value)) {
                foreach ($value as $key => $item) {
                    $value[$key] = $this->doReverseTransform($item);
                }
            } else {
                $value = $this->doReverseTransform($value);
            }
        } catch (\Exception $e) {
        }

        return $value;
    }

    private function doTransform($value): string
    {
        return $value instanceof UuidInterface ? $value->toString() : $value;
    }

    private function doReverseTransform($value): UuidInterface
    {
        return Uuid::fromString($value);
    }
}
