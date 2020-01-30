<?php

namespace Filipedanielski\Gridfs\Eloquent;

use Filipedanielski\Gridfs\Support\ReadableFilesize;
use Illuminate\Contracts\Support\Responsable;
use Jenssegers\Mongodb\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel implements Responsable
{
    use Concerns\HasBucket,
        Concerns\UploadsFiles;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [ 'uploadDate' ];

    /**
     * Append a readable version of the length attribute 
     *
     * @return string
     */
    public function getReadableLengthAttribute(): string
    {
        return ReadableFilesize::readableFilesize($this->length);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        //
    }
}
