<?php

namespace Filipedanielski\Gridfs\Tests;

use Filipedanielski\Gridfs\Tests\Support\TestModels\Photo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ModelTest extends TestCase
{
    public static function tearDownAfterClass(): void
    {
        $bucket = Photo::connectToBucket();
        $bucket->drop();
    }

    public function testUpload()
    {
        $stub = __DIR__ . '/../Support/testfiles/test.png';
        $name = Str::random(8) . '.png';
        $path = sys_get_temp_dir() . '/' . $name;

        copy($stub, $path);

        $file = new UploadedFile($path, $name, 'image/png', filesize($path), null, true);

        Photo::upload($file, [], $name);

        $this->assertDatabaseHas('photos.files', ['filename' => $name]);

        unlink($path);
    }

    public function testCreate()
    {
        $stub = __DIR__ . '/../Support/testfiles/test.png';
        $name = Str::random(8) . '.png';
        $path = sys_get_temp_dir() . '/' . $name;

        copy($stub, $path);

        $file = new UploadedFile($path, $name, 'image/png', filesize($path), null, true);

        Photo::create([
            'file' => $file,
            'filename' => $name,
            'fake_user_id' => 2
        ]);

        $this->assertDatabaseHas('photos.files', [
            'filename' => $name,
            'metadata.fake_user_id' => 2
        ]);

        unlink($path);
    }
}
