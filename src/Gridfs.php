<?php

namespace Filipedanielski\Gridfs;

trait Gridfs
{
    /*
    |--------------------------------------------------------------------------
    | Upload functions
    |--------------------------------------------------------------------------
    */
    public function upload($file, $metadata = [], $filename = null){
        $bucket = $this->connectToBucket();

        $source = fopen($file->path(), 'rb');

        return $bucket->uploadFromStream(
            $filename ?: $file->hashName(),
            $source,
            ($metadata != []) ? ['metadata' => $metadata ] : []
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Download functions
    |--------------------------------------------------------------------------
    */
    public function download($id, $revision = null){
        $bucket = $this->connectToBucket();

        $stream = $bucket->openDownloadStream($id);
        $metadata = $bucket->getFileDocumentForStream($stream);

        if($revision != null){
            unset($stream);
            $stream = $bucket->openDownloadStreamByName(
                $metadata->filename, ['revision' => $revision]
            );

            unset($metadata);
            $metadata = $bucket->getFileDocumentForStream($stream);
        }

        $contents = stream_get_contents($stream);

        return $this->prepareDownload($contents, $metadata);
    }

    public function downloadByFilename($filename, $revision = null){
        $bucket = $this->connectToBucket();

        if($revision != null){
            $stream = $bucket->openDownloadStreamByName($filename);
            $metadata = $bucket->getFileDocumentForStream($stream);
        } else {
            $stream = $bucket->openDownloadStreamByName(
                $filename, ['revision' => $revision]
            );
            $metadata = $bucket->getFileDocumentForStream($stream);
        }

        $contents = stream_get_contents($stream);

        return $this->prepareDownload($contents, $metadata);
    }

    public function downloadOriginal($filename){
        $bucket = $this->connectToBucket();

        $stream = $bucket->openDownloadStreamByName(
            $filename, ['revision' => 0]
        );
        $metadata = $bucket->getFileDocumentForStream($stream);

        $contents = stream_get_contents($stream);

        return $this->prepareDownload($contents, $metadata);
    }

    public function prepareDownload($contents, $metadata){
        return response($contents)
            ->withHeaders([
                'Content-Type'              => 'application/octet-stream',
                'Content-Disposition'       => "attachment; filename=" . $metadata->filename,
                'Content-Transfer-Encoding' => 'Binary',
                'Content-Description'       => 'File Transfer',
                'Pragma'                    => 'public',
                'Expires'                   => '0',
                'Cache-Control'             => 'must-revalidate',
                'Content-Length'            => "{$metadata->length}"
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Search functions
    |--------------------------------------------------------------------------
    */
    public function findOne($filter = [], array $options = []){
        $bucket = $this->connectToBucket();

        return $bucket->findOne($filter, $options);
    }

    /*
    |--------------------------------------------------------------------------
    | Connection and configuration functions
    |--------------------------------------------------------------------------
    */
    public function connectToBucket(){
        return \Illuminate\Support\Facades\DB::connection(
            $this->connection ?: $this->app['config']['database.default']
        )
        ->getMongoDB()
        ->selectGridFSBucket(
            ['bucketName' => $this->getBucketName()]
        );
    }

    public function getBucketName(){
        return $this->bucket ?: "gridfs";
    }
}