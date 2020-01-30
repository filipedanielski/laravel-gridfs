<?php

namespace Filipedanielski\Gridfs\Tests\Support\TestModels;

use Jenssegers\Mongodb\Eloquent\Model;

class User extends Model
{
    protected $connection = 'mongodb';
    protected static $unguarded = true;

    public function photos()
    {
        return $this->hasMany(Photo::class, "metadata.user_id");
    }
}
