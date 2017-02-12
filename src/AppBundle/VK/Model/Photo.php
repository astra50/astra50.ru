<?php

namespace AppBundle\VK\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Photo extends Model
{
    public $pid;
    public $aid;
    public $owner_id;
    public $user_id;
    public $src;
    public $src_big;
    public $src_small;
    public $src_xbig;
    public $src_xxbig;
    public $width;
    public $height;
    public $text;
    public $created;
}
