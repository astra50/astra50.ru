<?php

declare(strict_types=1);

namespace App\Form\Transformer;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Uuid\Uuid;

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
        if (is_array($value)) {
            foreach ((array) $value as $key => $item) {
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
        try {
            if (is_array($value)) {
                foreach ((array) $value as $key => $item) {
                    $value[$key] = $this->doReverseTransform($item);
                }
            } else {
                $value = $this->doReverseTransform($value);
            }
        } catch (\Exception $e) {
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    private function doTransform($value)
    {
        return $value instanceof UuidInterface ? $value->toString() : $value;
    }

    /**
     * @param $value
     *
     * @return UuidInterface
     */
    private function doReverseTransform($value)
    {
        return Uuid::fromString($value);
    }
}
