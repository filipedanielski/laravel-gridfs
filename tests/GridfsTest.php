<?php

use Illuminate\Http\UploadedFile;

class GridfsTest extends TestCase
{
    public static function tearDownAfterClass(){
        $bucket = Photo::connectToBucket();
        $bucket->drop();
    }

    public function testUpload(){
        $stub = __DIR__.'/stubs/test.png';
        $name = str_random(8).'.png';
        $path = sys_get_temp_dir().'/'.$name;

        copy($stub, $path);

        $file = new UploadedFile($path, $name, 'image/png', filesize($path), null, true);

        Photo::upload($file, [], $name);

        $this->assertDatabaseHas('photos.files', ['filename' => $name]);

        unlink($path);
    }

    public function testDownload(){
        $photo = Photo::first();

        $download = $photo->download();

        $this->assertTrue(preg_match('/(error|notice)/i', $download) === 0);
    }

    public function testDownloadById(){
        $photo = Photo::searchOne(["length" => 842]);

        $download = Photo::download($photo->_id);

        $this->assertTrue(preg_match('/(error|notice)/i', $download) === 0);
    }

    public function testZipDownload(){
        $photos = Photo::search(["length" => 842]);

        $download = Photo::downloadZip($photos);

        $this->assertTrue(preg_match('/(error|notice)/i', $download) === 0);
    }

    public function testRemove(){
        $photo = Photo::searchOne(["length" => 842]);

        Photo::remove($photo->_id);

        $this->assertDatabaseMissing('photos.files', ['filename' => $photo->filename]);
    }
}