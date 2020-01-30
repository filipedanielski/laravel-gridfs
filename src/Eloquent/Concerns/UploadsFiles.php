<?php

namespace Filipedanielski\Gridfs\Eloquent\Concerns;

use Illuminate\Http\UploadedFile;

trait UploadsFiles
{
    /**
     * Uploads a file to a GridFS bucket.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array                         $metadata
     * @param string                        $filename
     *
     * @return \MongoDB\BSON\ObjectId
     */
    protected function upload($file, $metadata = [], $filename = null)
    {
        $bucket = $this->connectToBucket();

        $source = fopen($file->path(), 'rb');

        return $bucket->uploadFromStream(
            $filename ?? $file->hashName(),
            $source,
            filled($metadata) ? ['metadata' => $metadata] : []
        );
    }

    /**
     * Save a new model and return the instance.
     *
     * @param array $attributes
     *
     * @throws \Exception
     *
     * @return \Filipedanielski\Gridfs\Eloquent\Model|$this
     */
    protected function create(array $attributes = [])
    {
        $attributes = collect($attributes);

        if (!$attributes->has('file')) {
            throw new Exception('A file attribute is required.');
        }

        $file = $attributes->pull('file');

        if (!$file instanceof UploadedFile) {
            throw new Exception('The file attribute must be a valid uploaded file.');
        }

        $filename = $attributes->pull('filename');

        return $this->upload($file, $attributes->toArray(), $filename);
    }
}
