<?php

namespace AppBundle\Uuid;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Uuid extends \Ramsey\Uuid\Uuid
{
    public function __toString()
    {
        return $this->getBytes();
    }
}
