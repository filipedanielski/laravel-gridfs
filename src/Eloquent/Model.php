<?php

namespace Filipedanielski\Gridfs\Eloquent;

use Illuminate\Contracts\Support\Responsable;
use Jenssegers\Mongodb\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel implements Responsable
{
    use Concerns\HasBucket;

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
