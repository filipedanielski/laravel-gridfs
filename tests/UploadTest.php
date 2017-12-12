<?php

use Illuminate\Http\UploadedFile;

class UploadTest extends TestCase
{
    public function testUpload(){
        $stub = __DIR__.'/stubs/test.png';
        $name = str_random(8).'.png';
        $path = sys_get_temp_dir().'/'.$name;

        copy($stub, $path);

        $file = new UploadedFile($path, $name, 'image/png', filesize($path), null, true);

        $photo = new Photo;
        $photo->upload($file, [], $name);

        $this->assertDatabaseHas('photos.files', ['filename' => $name]);

        unlink($path);
    }

    
    public function testZipDownload(){
        $photo = new Photo();
        $photos = $photo->find(["length" => 842]);

        $download = $photo->downloadZip($photos);

        $this->assertTrue(preg_match('/(error|notice)/i', $download) === 0);
    }
}