<?php

namespace Filipedanielski\Gridfs\Eloquent\Concerns;

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
}
