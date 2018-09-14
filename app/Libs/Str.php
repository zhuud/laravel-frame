<?php

namespace App\Libs;

use Illuminate\Support\Str as LaravelStr;

/**
 * Class Str
 * @package App\Http\Libs
 */
class Str extends LaravelStr
{
    /**
     * 生成唯一字符串
     *
     * @return string
     */
    public static function genUniqueId() : string
    {
        $microTime  = microtime(true);

        return base_convert(floor($microTime * 1000), 10, 36);
    }

    /**
     * json数组 不编码，不转义
     *
     * @param array $arr
     * @return false|string
     */
    static public function jsonEncode(array $arr) : string
    {
        return json_encode($arr, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }
}