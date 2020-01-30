<?php

namespace Filipedanielski\Gridfs;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class FileStream implements Responsable
{
    /**
     * @var string
     */
    protected $zipName;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $files;

    public static function create(string $zipName)
    {
        return new static($zipName);
    }

    public function __construct(string $zipName)
    {
        $this->zipName = $zipName;

        $this->files = collect();
    }
}
