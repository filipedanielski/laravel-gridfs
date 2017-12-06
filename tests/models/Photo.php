<?php

use Filipedanielski\Gridfs\Gridfs;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Photo extends Eloquent
{
    use Gridfs;

    protected $connection = 'mongodb';
    protected $collection = 'photos.files';
    protected $bucket = 'photos';
    protected static $unguarded = true;
}