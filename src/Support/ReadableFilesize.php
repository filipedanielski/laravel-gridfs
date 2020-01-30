<?php

namespace Filipedanielski\Gridfs\Support;

class ReadableFilesize
{
    public static function readableFilesize($bytes, $decimals = 2): string
    {
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}
