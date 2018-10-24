<?php

namespace App\Libs;

use Exception;
use LogicException;
use UnexpectedValueException;

/**
 * Class Error
 * @package App\Http\Libs
 */
class Error
{
    /**
     * @param string $type
     * @throws Exception
     */
    public static function typeErr(string $type = 'default')
    {
        $info = config('error.' . $type, 'error.default');

        throw new Exception($info['msg'], $info['code']);
    }

    /**
     * @param string $message
     */
    public static function programErr(string $message = '程序错误！')
    {
        throw new LogicException($message, config('error.program.code', 2000000));
    }

    /**
     * @param string $message
     */
    public static function apiErr(string $message = '请求错误！')
    {
        throw new UnexpectedValueException($message, config('error.program.code', 7000000));
    }
}