<?php

namespace Filipedanielski\Gridfs;

trait Gridfs
{
    public function upload($file, $metadata = [], $filename = null){
        $bucket = $this->connectToBucket();

        $source = fopen($file->path(), 'rb');

        return $bucket->uploadFromStream(
            $filename ?: $file->hashName(),
            $source,
            ($metadata != []) ? ['metadata' => $metadata ] : []
        );
    }

    public function download($id){
        $bucket = $this->connectToBucket();

        $stream = $bucket->openDownloadStream($id);
        $metadata = $bucket->getFileDocumentForStream($stream);

        $contents = stream_get_contents($stream);

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

    public function findOne($filter = [], array $options = []){
        $bucket = $this->connectToBucket();

        return $bucket->findOne($filter, $options);
    }

    
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