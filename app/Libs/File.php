<?php

namespace App\Libs;

/**
 * Class File
 * @package App\Libs
 */
class File
{
    public static function genFileName()
    {
        $microTime  = microtime(true);

        return base_convert(floor($microTime * 1000), 10, 36);
    }
}