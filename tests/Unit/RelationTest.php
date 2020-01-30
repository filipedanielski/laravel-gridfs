<?php

namespace Filipedanielski\Gridfs\Tests;

use Filipedanielski\Gridfs\Tests\Support\TestModels\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class RelationTest extends TestCase
{
    public function testRelationCreate()
    {
        $stub = __DIR__.'/../Support/testfiles/test.png';
        $name = Str::random(8).'.png';
        $path = sys_get_temp_dir().'/'.$name;

        copy($stub, $path);

        $file = new UploadedFile($path, $name, 'image/png', filesize($path), null, true);

        $user = new User();
        $user->save();

        $user->photos()->create([
            'file'     => $file,
            'filename' => $name,
        ]);

        $this->assertDatabaseHas('photos.files', [
            'filename'         => $name,
            'metadata.user_id' => $user->_id,
        ]);

        unlink($path);
    }
}
