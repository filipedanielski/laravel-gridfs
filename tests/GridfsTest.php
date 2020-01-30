<?php

namespace Filipedanielski\Gridfs\Tests;

use Filipedanielski\Gridfs\Tests\Support\TestModels\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class GridfsTest extends TestCase
{
    public static function tearDownAfterClass(): void
    {
        $bucket = Document::connectToBucket();
        $bucket->drop();
    }

    public function testUpload()
    {
        $stub = __DIR__ . '/Support/testfiles/test.png';
        $name = Str::random(8) . '.png';
        $path = sys_get_temp_dir() . '/' . $name;

        copy($stub, $path);

        $file = new UploadedFile($path, $name, 'image/png', filesize($path), null, true);

        Document::upload($file, [], $name);

        $this->assertDatabaseHas('documents.files', ['filename' => $name]);

        unlink($path);
    }

    public function testDownload()
    {
        $document = Document::first();

        $download = $document->download();

        $this->assertTrue(preg_match('/(error|notice)/i', $download) === 0);
    }

    public function testDownloadById()
    {
        $document = Document::searchOne(['length' => 842]);

        $download = Document::download($document->_id);

        $this->assertTrue(preg_match('/(error|notice)/i', $download) === 0);
    }

    public function testZipDownload()
    {
        $documents = Document::search(['length' => 842]);

        $download = Document::downloadZip($documents);

        $this->assertTrue(preg_match('/(error|notice)/i', $download) === 0);
    }

    public function testRemove()
    {
        $document = Document::searchOne(['length' => 842]);

        Document::remove($document->_id);

        $this->assertDatabaseMissing('documents.files', ['filename' => $document->filename]);
    }
}
