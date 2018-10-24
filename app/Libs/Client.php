<?php

namespace App\Libs;

use Throwable;
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
     * @param array $config
     * @return Client
     */
    public static function parse(array $config = [])
    {
        return  new static($config);
    }

    /**
     * 签名
     *
     * @param array $params
     * @param string $key
     * @return array
     */
    public static function sign(array $params, string $key = '') : array
    {
        $params += [
            'timeStamp' => microtime(true),
        ];

        $sorted = Arr::kSort($params);

        if (!empty($key)) {

            $sorted += ['secret' => config($key),];
        }

        $signature  = sha1(http_build_query($sorted));

        $params     += ['signature' => $signature,];

        return  $params;
    }

    /**
     * @param $method
     * @param string $uri
     * @param array $options
     * @param bool $throw
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri = '', array $options = [], bool $throw = true)
    {
        try {
            $response = parent::request($method, $uri, $options);
        } catch (Throwable $t) {

            logger("服务器发送请求错误，文件：{$t->getFile()}，行数：{$t->getLine()}，错误信息：{$t->getMessage()}");
            $throw && Error::apiErr();
        }

        return $response;
    }
}