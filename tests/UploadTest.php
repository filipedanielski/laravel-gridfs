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
        $photo->upload($file);
    }
}