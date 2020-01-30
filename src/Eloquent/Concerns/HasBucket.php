<?php

namespace Filipedanielski\Gridfs\Eloquent\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasBucket
{
    /**
     * The bucket associated with the model.
     *
     * @var string
     */
    protected $bucket;

    /**
     * Get the bucket associated with the model.
     *
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket ?? Str::snake(Str::pluralStudly(class_basename($this)));
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->getBucket() . '.files';
    }

    /**
     * Start a connection with the GridFS bucket
     * 
     * @return \Illuminate\Database\Connection
     */
    protected function connectToBucket()
    {
        return DB::connection(
            $this->connection ?: $this->app['config']['database.default']
        )
            ->getMongoDB()
            ->selectGridFSBucket(
                ['bucketName' => $this->getBucket()]
            );
    }
}
