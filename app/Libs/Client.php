<?php

namespace App\Libs;

use GuzzleHttp\Client AS GuzzleClient;

/**
 * Class Client
 * @package App\Http\Libs
 */
class Client extends GuzzleClient
{

    /**
     * Create a GuzzleHttp\Client instance
     *
     * @return static
     */
    public static function parse()
    {
        return  new static();
    }

    /**
     * 签名
     *
     * @param array $params
     * @return array
     */
    public static function sign(array $params) : array
    {
        $params += [
            'expired' => microtime(true),
        ];

        $sorted = Arr::kSort($params);
        $sorted += [
            'secret'  => config($params['key']),
        ];

        $signature  = sha1(http_build_query($sorted));

        $params     += ['signature' => $signature,];

        return  $params;
    }

    /**
     * 获取结果
     *
     * @param   string  $response   响应内容
     * @return  mixed               结果
     */
    public static function getResult($response)
    {
        return  json_decode($response, true);
    }
}