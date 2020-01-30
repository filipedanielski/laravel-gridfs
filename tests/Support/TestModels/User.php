<?php

namespace Filipedanielski\Gridfs\Tests\Support\TestModels;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    protected $connection = 'mongodb';
    protected static $unguarded = true;

    public function photos()
    {
    }
}
