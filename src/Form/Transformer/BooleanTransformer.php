<?php

declare(strict_types=1);

namespace App\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class BooleanTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        dump($value);
        if (null === $value) {
            return;
        }

        if (!is_bool($value)) {
            throw new TransformationFailedException('Value must be type of boolean');
        }

        if (false === $value) {
            return 'f';
        }

        return 't';
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        dump($value);
        $haystack = ['t', 'f'];

        if (!in_array($value, $haystack, true)) {
            throw new TransformationFailedException('Expected "t" or "f" string');
        }

        return $haystack[$value];
    }
}
