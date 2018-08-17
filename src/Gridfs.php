<?php

namespace Filipedanielski\Gridfs;

use ZipStream\ZipStream;

trait Gridfs
{
    /*
    |--------------------------------------------------------------------------
    | Upload functions
    |--------------------------------------------------------------------------
    */
    private function upload($file, $metadata = [], $filename = null){
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
    private function download($id = null, $revision = null){
        $bucket = $this->connectToBucket();

        $stream = $this->getFileStream($bucket, $id);
        $metadata = $this->getFileMetadata($bucket, $stream);
        
        if($revision != null){
            unset($stream);
            $stream = $this->getFileByRevision($bucket, $metadata->filename, $revision);

            unset($metadata);
            $metadata = $this->getFileMetadata($bucket, $stream);
        }

        $contents = stream_get_contents($stream);

        return $this->prepareDownload($contents, $metadata);
    }

    private function downloadWithoutHeaders($id = null, $revision = null){
        $bucket = $this->connectToBucket();

        $stream = $this->getFileStream($bucket, $id);
        
        if($revision != null){
            $metadata = $this->getFileMetadata($bucket, $stream);

            unset($stream);
            $stream = $this->getFileByRevision($bucket, $metadata->filename, $revision);
        }

        return stream_get_contents($stream);
    }

    private function downloadByFilename($filename, $revision = null){
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

    private function downloadOriginal($filename){
        $bucket = $this->connectToBucket();

        $stream = $bucket->openDownloadStreamByName(
            $filename, ['revision' => 0]
        );
        $metadata = $bucket->getFileDocumentForStream($stream);

        $contents = stream_get_contents($stream);

        return $this->prepareDownload($contents, $metadata);
    }

    /*
    |--------------------------------------------------------------------------
    | .zip download functions
    |--------------------------------------------------------------------------
    */
    private function downloadZip($cursor){
        $bucket = $this->connectToBucket();
        
        list($tmp, $zipstream) = $this->getTmpFileStream();
        $zip = new ZipStream(null, array(
            ZipStream::OPTION_OUTPUT_STREAM => $zipstream
        ));

        $files = $cursor->toArray();

        foreach($files as $file){
            $stream = $bucket->openDownloadStream($file->_id);
            $metadata = $bucket->getFileDocumentForStream($stream);

            $zip->addFileFromStream($metadata->filename, $stream);
        }

        $zip->finish();
        fclose($zipstream);
    }

    protected function getTmpFileStream()
    {
        $tmp = tempnam(sys_get_temp_dir(), 'zipstream');
        $stream = fopen($tmp, 'w+');

        return array($tmp, $stream);
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare functions
    |--------------------------------------------------------------------------
    */
    private function getFileStream($bucket, $id = null){
        $stream = $bucket->openDownloadStream(
            ($id != null) ? $id : (new \MongoDB\BSON\ObjectId($this->_id))
        );

        return $stream;
    }

    private function getFileMetadata($bucket, $stream){
        return $bucket->getFileDocumentForStream($stream);
    }

    private function getFileByRevision($bucket, $filename, $revision){
        return $bucket->openDownloadStreamByName(
            $filename, ['revision' => $revision]
        );
    }

    protected function prepareDownload($contents, $metadata){
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
    | Delete functions
    |--------------------------------------------------------------------------
    */
    private function remove($id = null){
        $bucket = $this->connectToBucket();

        return $bucket->delete(($id != null) ? $id : (new \MongoDB\BSON\ObjectId($this->_id)));
    }

    /*
    |--------------------------------------------------------------------------
    | Search functions
    |--------------------------------------------------------------------------
    */
    private function searchOne($filter = [], array $options = []){
        $bucket = $this->connectToBucket();

        return $bucket->findOne($filter, $options);
    }

    private function search($filter = [], array $options = []){
        $bucket = $this->connectToBucket();
        
        return $bucket->find($filter, $options);
    }

    /*
    |--------------------------------------------------------------------------
    | Connection and configuration functions
    |--------------------------------------------------------------------------
    */
    private function connectToBucket(){
        return \Illuminate\Support\Facades\DB::connection(
            $this->connection ?: $this->app['config']['database.default']
        )
        ->getMongoDB()
        ->selectGridFSBucket(
            ['bucketName' => $this->getBucketName()]
        );
    }

    private function getBucketName(){
        return $this->bucket ?: "gridfs";
    }

    /*
    |--------------------------------------------------------------------------
    | Magic methods
    |--------------------------------------------------------------------------
    */
    public function __call($method, $parameters)
    {
        $reflection = new \ReflectionClass('Filipedanielski\Gridfs\Gridfs');
        $trait_functions = $reflection->getMethods();

        foreach($trait_functions as $function){
            if ($method == $function->name){
                return call_user_func_array([$this, $method], $parameters);
            }
        }
        return parent::__call($method, $parameters);
    }

    public static function __callStatic($method, $parameters)
    {
        $instance = (new static);
        return call_user_func_array([$instance, $method], $parameters);
    }
}