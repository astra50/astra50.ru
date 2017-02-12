<?php

namespace AppBundle\VK\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class Model
{
    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
