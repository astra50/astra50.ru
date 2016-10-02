<?php

namespace AppBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class MoneyTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        return (int) $value;
    }
}
