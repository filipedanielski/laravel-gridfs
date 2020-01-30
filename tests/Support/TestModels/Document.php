<?php

namespace Filipedanielski\Gridfs\Tests\Support\TestModels;

use Filipedanielski\Gridfs\Gridfs;
use Jenssegers\Mongodb\Eloquent\Model;

class Document extends Model
{
    use Gridfs;

    protected $connection = 'mongodb';
    protected $collection = 'documents.files';
    protected $bucket = 'documents';
}
