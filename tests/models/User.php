<?php

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    protected $connection = 'mongodb';
    protected static $unguarded = true;

    public function photos()
    {
    }
}
