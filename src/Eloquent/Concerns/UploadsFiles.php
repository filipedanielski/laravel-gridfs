<?php

namespace Filipedanielski\Gridfs\Eloquent\Concerns;

trait UploadsFiles
{
    /**
     * Uploads a file to a GridFS bucket.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  array $metadata
     * @param  string $filename
     * 
     * @return \MongoDB\BSON\ObjectId
     */
    protected function upload($file, $metadata = [], $filename = null)
    {
        $bucket = $this->connectToBucket();

        $source = fopen($file->path(), 'rb');

        return $bucket->uploadFromStream(
            $filename ?: $file->hashName(),
            $source,
            ($metadata != []) ? ['metadata' => $metadata] : []
        );
    }
}
