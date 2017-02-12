<?php

namespace AppBundle\VK\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Album extends Model
{
    public $aid;
    public $thumb_id;
    public $owner_id;
    public $title;
    public $description;
    public $created;
    public $updated;
    public $size;
    public $can_upload;
    public $thumb_src;
}
