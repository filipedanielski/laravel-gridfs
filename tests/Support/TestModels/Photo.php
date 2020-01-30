<?php

namespace Filipedanielski\Gridfs\Tests\Support\TestModels;

use Filipedanielski\Gridfs\Eloquent\Model;

class Photo extends Model
{
    protected $connection = 'mongodb';
    protected static $unguarded = true;

    /**
     * @var array
     */
    public $appends = [
        'readableLength',
    ];
}
